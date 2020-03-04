<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Log;
use QuickBooksOnline\API\Facades\Estimate;
use Illuminate\Support\Facades\Auth;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Exception\SdkException;

use App\JobActions;
use App\Traits\ConvertPrices;


class Job extends Model
{
    use ConvertPrices;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'actual_end_date',
        'address_line_1',
        'address_line_2',
        'agreed_start_date',
        'bid_price',
        'city',
        'completed_bid_date',
        'contractor_id',
        'customer_id',
        'job_name',
        'location_id',
        'payment_type',
        'state',
        'status',
        'zip',
    ];


    /*
 * *********************************
 * Relationships
 * **********************************/

    public function customer()
    {
        return $this->belongsTo(User::class)->with('customer');
    }

    public function contractor()
    {
        return $this->belongsTo(User::class)->with('contractor');
    }

    public function jobStatuses()
    {
        return $this->hasMany(JobStatus::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);

//        use App\Job;
//        Job::find(1)->
//        with(
//            [
//                'jobTasks:id,job_id',
//                'jobTasks.bidContractorJobTasks:id,job_task_id',
//                'jobTasks.images:id,job_task_id',
//                'location:id,location_id'
//            ])->
//            select(['id', 'bid_price'])->where('id', 1)->get()->first();
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')
            ->withTimestamps();
    }

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'job_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class, 'job_id');
    }


    public function setStatusToInProgress()
    {

        if (empty($this->checkStatus('in_progress'))) {
            $js = new JobStatus();
            $js->setStatus($this->id, 'in_progress');
        }
    }

    private function checkStatus($status)
    {
        return JobStatus::where("status", "=", $status)
            ->where("job_id", "=", $this->id)->get()->first();
    }

    /**
     * All subs who have a job task on this job
     *
     * @return void
     */
    public function subs()
    {
        $jobTasks = $this->jobTasks()->get();
        $subs = [];
        foreach ($jobTasks as $jobTask) {
            $subs[] = $jobTask->contractor();
        }
        return $subs;
    }

    /**
     * Return related JobActions Model - create it
     * if it doesn't exist
     *
     * @return JobActions model
     */
    public function jobActions()
    {
        $jobActions = $this->hasOne(JobActions::class);

        if ($jobActions->exists()) {
            $jobActions = $jobActions->first();
        } else {
            $jobActions = $this->createJobActions();
        }

        return $jobActions;
    }

    public function approveJob(
        $address,
        $agreedStartDate,
        $customerLocationId,
        $jobLocationSameAsHome
    )
    {
        // TODO: what date needs to be updated here?
        $this->agreed_start_date = $agreedStartDate;
        $this->status = __('job.approved');

        DB::transaction(function () use ($jobLocationSameAsHome, $customerLocationId, $address) {
            if ($jobLocationSameAsHome) {
                $this->location_id = $customerLocationId;
            } else {
                $this->newLocation($address);
            }
            $this->save();
            // approve all tasks associated with this job, any exceptions?
            JobTask::where('job_id', $this->id)
                //->where('bid_id', '!=', 'NULL') // update unless no bid connected to the job task
                ->update(['status' => __('bid_task.approved_by_customer')]);
            JobTask::where('job_id', $this->id)
                ->where('start_when_accepted', true)
                ->update(['start_date' => Carbon::now()]);
        });
    }

    public static function jobName($jobName = '')
    {
        if (empty($jobName)) {
            // what if there are no Jobs?
            if (empty(Job::all()->last()->id)) {
                $nextJob = 1;
            } else {
                $nextJob = Job::all()->last()->id + 1;
            }
            $jobName = "{$nextJob}";
        }
        return $jobName;
    }

    public function updateJobPrice()
    {
        $jobTasks = $this->jobTasks()->get();
        $totalPrice = 0;
        foreach ($jobTasks as $jobTask) {
            $totalPrice = $totalPrice + $jobTask->cust_final_price;
        }
        $this->bid_price = $totalPrice;

        try {
            $this->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

    }

    public static function createQuickBooksEstimate($customer, $task, $job, $jt, $customer_quickBooks_Id)
    {
        $qb = new Quickbook();
        return $qb->createEstimate($customer, $task, $job, $jt, $customer_quickBooks_Id);
    }

    public function updateQuickBooksEstimate($task, $job, $jobTask)
    {
        $accessToken = session('sessionAccessToken');
        $qbUser = Quickbook::select()->where('user_id', '=', Auth::user()->getAuthIdentifier())->get()->first();
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'accessTokenKey' => $accessToken->getAccessToken(),
            'refreshTokenKey' => $qbUser->refresh_token,
            'QBORealmID' => $qbUser->company_id,
            'baseUrl' => "development"
        ));

        $unitPrice = $this->convertToDollars($task->unit_price);
        $bidPrice = $this->convertToDollars($job->bid_price);

        $estimate = $dataService->FindbyId('estimate', $job->qb_estimate_id);
        $theResourceObj = Estimate::update($estimate, [
            "Line" => [
                [
                    "Description" => $task->description,
                    "Amount" => $unitPrice,
                    "DetailType" => "SalesItemLineDetail",
                    "SalesItemLineDetail" => [
                        "ItemRef" => [
                            "value" => $task->item_id,
                            "name" => $task->name
                        ],
                        "UnitPrice" => $unitPrice,
                        "Qty" => $jobTask->qty,
                        "TaxCodeRef" => [
                            "value" => "NON"
                        ]
                    ]
                ],
                [
                    "Amount" => $bidPrice,
                    "DetailType" => "SubTotalLineDetail",
                    "SubTotalLineDetail" => []
                ]
            ],
        ]);
        $resultingObj = $dataService->Update($theResourceObj);
        $error = $dataService->getLastError();
        if ($error) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
        }

        return $resultingObj;
    }

    public function setEarliestStartDateToTask(JobTask $jobTask)
    {
        $earliestDate = JobTask::findEarliestStartDate($this->id);
        $this->updateJobAgreedStartDate($earliestDate);
    }

    public function getJobId()
    {
        $jobId = DB::select('SELECT id FROM jobs ORDER BY id DESC LIMIT 1');

        if ($jobId == []) {
            return 1;
        } else {
            return $jobId[0]->id + 1;
        }

    }

    public function hasAQuickbookEstimateBeenCreated()
    {
        return $this->qb_estimate_id != 'NULL';
    }

    public function createEstimate($customer_id,
                                   $job_name,
                                   $contractor_id,
                                   $paymentType)
    {

        $jobId = $this->getJobId();

        if (empty(User::find($customer_id)->customer()->first()->location_id)) {
            $attributes = [
                'job_id' => $jobId,
                'contractor_id' => $contractor_id,
                'customer_id' => $customer_id,
                'job_name' => $job_name,
                'payment_type' => $paymentType,
                'status' => __("status.bid.initiated"),
                'location_id' => null
            ];
        } else {
            $attributes = [
                'job_id' => $jobId,
                'contractor_id' => $contractor_id,
                'customer_id' => $customer_id,
                'job_name' => $job_name,
                'payment_type' => $paymentType,
                'status' => __("status.bid.initiated"),
                'location_id' => User::find($customer_id)->customer()->first()->location_id
            ];
        }

        $this->fill($attributes);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::critical('Failed to create a bid: ' . $e->getMessage());
            return false;
        }

        return true;

//        // not the best way but autoincrementing the id number
//        // TODO: find a better way but this works for now
//        $jobId = DB::select('SELECT id FROM jobs ORDER BY id DESC LIMIT 1');
//
//        if ($jobId == []) {
//            $this->id = 1;
//        } else {
//            $this->id = $jobId[0]->id + 1;
//        }
//        $this->contractor_id = $contractor_id; // actually the user id and not the contractor Id
////        $this->contractor_id = Auth::user()->id; // actually the user id and not the contractor Id
//        $this->customer_id = $customer_id;       // also the user Id and not the customer Id
//        $this->job_name = $job_name;
//        $this->status = __("status.bid.initiated");
//        if (User::find($customer_id)->customer()->first() !== null) {
//            $this->location_id = User::find($customer_id)->customer()->first()->location_id;
//        }
//
//        try {
//            $this->save();
//        } catch (\Exception $e) {
//            Log::critical('Failed to create a bid: ' . $e->getMessage());
//            return false;
//        }
//        return true;
    }

    /**
     * Accept the job
     *
     * @return bool did this action succeed
     */
    public function acceptJob()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();

        $jobActions->job_accepted = true;
        $jobActions->job_accepted_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: accept job - ' . $e->getMessage());
            return false;
        }
    }

    public function newLocation($address)
    {
        $location = new Location();
        $location->address_line_1 = $address['addressLine1'];
        $location->address_line_2 = $address['addressLine2'];
        $location->city = $address['city'];
        $location->state = $address['state'];
        $location->zip = $address['zip'];

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }
    }

    public function updateStartDate($date)
    {

    }

    /**
     * Set job as completed if all child task are paid for
     *
     * @return void
     */
    public function setJobAsCompleted()
    {
        if ($this->getCountOfUnpaidTasks() >= 1) {
            return;
        }

        $this->status = __('job.completed');

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update Job' . $e->getMessage());
        }
    }

    public function updateJobAgreedStartDate($date)
    {
        $this->agreed_start_date = $date;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Start Date was unsuccessful: ' . $e->getMessage());
        }
    }

    public function getCountOfUnpaidTasks()
    {
        return count($this->jobTasks()->where('status', '!=', __('bid_task.customer_sent_payment'))->get());
    }

    public function getArea()
    {
//        dd($this->location);
        $location = $this->location()->first();
        if (!empty($location)) {
            return $location->area;
        }
        return response()->json(["message" => "Could Not Save Get the Area"], 404);
    }


    // TODO: Refactor for best practice
    public function updateArea($area)
    {
        $location = $this->location()->first();

        $location->area = $area;

        try {
            $location->save();
            return response()->json($location, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Could Not Save Area"], 404);
        }
    }

    /**
     * Decline the job
     *
     * @return bool did this action succeed
     */
    public function declineJob()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();

        $jobActions->job_declined = true;
        $jobActions->job_declined_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: decline job - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve the job
     *
     * @return bool did this action succeed
     */
    public function approveJobAction()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();
        $jobActions->job_approved = true;
        $jobActions->job_approved_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: approve job - ' . $e->getMessage());
            return false;
        }
    }

    public function changeJobStatus($job, $message)
    {
        $this->status = $message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't update job status message.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public function jobTotal()
    {
        // for each task that is related to the job -> SUM(qty * cust_final_price)
        $jt = $this->jobTasks()->where("deleted_at", "=", NULL)->get();

        $bid_price = 0;

        foreach ($jt as $j) {
            $bid_price = $bid_price + ($j->qty * $j->unit_price);
        }

        $this->bid_price = $bid_price;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Job total could not be updated: ' . $e->getMessage());
            return false;
        }
        return true;

    }

    /**
     * Helper function to create a JobActions
     * related to this Job model
     *
     * @return void
     */
    public function createJobActions()
    {

        if ($this->id == null) {
            return false;
        }

        $jobActions = new JobActions();
        $jobActions->job_id = $this->id;

        try {
            $jobActions->save();
            return $jobActions;
        } catch (\Exception $e) {
            Log::error('JobActions: creating new JobActions - ' . $e->getMessage());
        }
    }

    /**
     * Update Job status
     *
     * @param string $status
     * @return bool
     */
    public function updateStatus($status)
    {
        if (!$this->updatable($status)) {
            return false;
        }

        $this->status = $status;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Status: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Can the status be changed to what its
     * trying to be changed to
     *
     * @param string $status
     * @return bool
     */
    public function updatable(string $status)
    {
        switch ($status) {
            case 'bid.cancel':
                return $this->isCancellable();
                break;
            case 'job.completed':
                return $this->isCompletable();
                break;
            default:
                return true; // TODO: testing, should be false
                break;
        }
    }

    private function isCancellable()
    {
        return $this->status === 'bid.sent';
    }

    private function isCompletable()
    {
        return $this->status === 'job.approved' && $this->allJobTasksResolved();
    }

    private function allJobTasksResolved()
    {
        $totalTasks = count(DB::table('job_task')->where('job_id', $this->id)->where('deleted_at', null)->get());
        $totalTasksResolved = count(DB::table('job_task')->where('job_id', $this->id)->where('status', 'bid_task.customer_sent_payment')->get());
        return $totalTasks === $totalTasksResolved;
    }

    /**
     * Declined message
     *
     * @param String $message
     * @return void
     */
    public function setJobDeclinedMessage(String $message)
    {
        $this->declined_Message = $message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Set Declined Job Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public static function getAllJobIdsForContractor($generalId)
    {
        return Job::where('contractor_id', '=', $generalId)->select(['id'])->get()->toArray();
    }

    public function markJobAsPaid($jobId)
    {
        $js = new JobStatus();
        $js->setStatus($jobId, 'paid');
    }

}
