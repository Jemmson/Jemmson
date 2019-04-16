<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Customer;
use App\ContractorCustomer;
use App\Location;

class Contractor extends Model
{
//    protected $fillable = [
//        'user_id',
//        'company_name',
//        'company_logo_name',
//        'email_method_of_contact',
//        'sms_method_of_contact',
//        'phone_method_of_contact',
//    ];

    protected $guarded = [];

    //

    public function jobs()
    {
        return $this->hasMany(Job::class, 'contractor_id', 'user_id');
    }

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'contractor_id', 'user_id');
    }

    public function time()
    {
        return $this->hasMany(Time::class, 'contractor_id', 'user_id');
    }

    public function numberOfJobsLeft()
    {
        return $this->free_jobs;
    }

    public function hasMoreFreeJobs()
    {
        return $this->free_jobs > 0;  // means there are more free jobs that exist for the contractor
    }

    public function isSubscribed()
    {

        return $this->user->current_billing_plan !== null; // means the contractor has subscribed

    }

    public function usesAccountingSoftware($softwareName)
    {
        $this->accounting_software = $softwareName;
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canCreateNewJob()
    {
        if ($this->isSubscribed()) {
            return true;
        } else if ($this->hasMoreFreeJobs()) {
            $this->subtractFreeJob();
            return true;
        } else {
            return false;
        }
    }

    public function checkAccountingSoftware()
    {
        return $this->accounting_software;
    }

    public function subtractFreeJob()
    {
        if ($this->free_jobs <= 0) {
            return true;
        }
        $this->free_jobs -= 1;
        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Subtract Free Job: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function customers()
    {
        return $this->belongsToMany(
            'App\Customer',
            'contractor_customer',
            'contractor_user_id',
            'customer_user_id'
        );
    }

    public function stripeExpress()
    {
        return $this->hasOne(StripeExpress::class, 'contractor_id', 'user_id');
    }

    public function tasks()
    {
        return $this
            ->belongsToMany(Task::class, 'contractor_id', 'user_id')->withPivot('base_price')
            ->withTimestamps();
    }

    /**
     * Get all tasks sent to the
     * current contractor as bids
     *
     * @return void
     */
    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class, 'contractor_id', 'user_id');
    }

    public function addContractorToBidForJobTable($contractorId, $jobTaskId, $taskId)
    {
        $proposedBidPrice = Task::find($taskId)->proposed_sub_price;
        // TODO: update this
        DB::table('bid_contractor_job_task')->insert(
            ['contractor_id' => $contractorId, 'job_task_id' => $jobTaskId, "bid_price" => $proposedBidPrice]
        );

    }

    public function checkIfContractorSetBidForATask($contractorId, $jobTaskId)
    {
        if (empty(DB::table('bid_contractor_job_task')
            ->select('job_task_id')
            ->where('contractor_id', '=', $contractorId)
            ->where('job_task_id', '=', $jobTaskId)
            ->get()[0])) {
            return true;
        } else {
            return false;
        }
    }

    public function updateLocation($request)
    {

        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->user_id;
            $location->default = true;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
            $location->area = $request->city;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }


    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function addLocation($qbCustomerData)
    {
        $location = new Location();

        if (!is_null($qbCustomerData[0]->BillAddr)) {
            $location->address_line_1 = $qbCustomerData[0]->BillAddr->Line1;
            $location->address_line_2 = $qbCustomerData[0]->BillAddr->Line2;
            $location->city = $qbCustomerData[0]->BillAddr->City;
            $location->state = $qbCustomerData[0]->BillAddr->CountrySubDivisionCode;
            $location->zip = $qbCustomerData[0]->BillAddr->PostalCode;
            $location->lat = $qbCustomerData[0]->BillAddr->Lat;
            $location->long = $qbCustomerData[0]->BillAddr->Long;
        }

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $location->id;
    }

    //TODO: if location is saved from QB data then it is not saved again once a customer registers

    public function addCustomerToUserTable($qbCustomerData, $locationId, $phone)
    {
        $customer = new \App\User();
        $customer->name = $qbCustomerData[0]->FullyQualifiedName;
        $customer->location_id = $locationId;

        if (!is_null($qbCustomerData[0]->PrimaryEmailAddr)) {
            $customer->email = $qbCustomerData[0]->PrimaryEmailAddr->Address;
        }

        $customer->usertype = 'customer';
        $customer->phone = $phone;

        $customer->first_name = $qbCustomerData[0]->GivenName;
        $customer->last_name = $qbCustomerData[0]->FamilyName;
        $customer->password = bcrypt(rand(100000, 999999) . 'gibberishslksdlkdslksdslkdsdlk');
        $customer->password_updated = false;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $customer->id;
    }

    public function addCustomerToCustomerTable($qbCustomerData, $locationId, $user_id)
    {
        $customer = new Customer();
        $customer->user_id = $user_id;
        $customer->location_id = $locationId;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
//        return $customer->id;
    }

    public function associateContractorToCustomerTable($user_id, $contractorId, $quickbooksId)
    {
        $customer = new ContractorCustomer();
        $customer->contractor_user_id = $contractorId;
        $customer->customer_user_id = $user_id;
        $customer->quickbooks_id = $quickbooksId;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function addUserIdToLocationsTable($user_id, $locationId)
    {
        $location = \App\Location::find($locationId);
        $location->user_id = $user_id;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function formatPhone($phone)
    {
        $phoneString = '';
        for ($i = 0; $i < strlen($phone); $i++) {
            if ($phone[$i] == '1') {
                $phoneString = $phoneString . 1;
            } else if ($phone[$i] == '2') {
                $phoneString = $phoneString . 2;
            } else if ($phone[$i] == '3') {
                $phoneString = $phoneString . 3;
            } else if ($phone[$i] == '4') {
                $phoneString = $phoneString . 4;
            } else if ($phone[$i] == '5') {
                $phoneString = $phoneString . 5;
            } else if ($phone[$i] == '6') {
                $phoneString = $phoneString . 6;
            } else if ($phone[$i] == '7') {
                $phoneString = $phoneString . 7;
            } else if ($phone[$i] == '8') {
                $phoneString = $phoneString . 8;
            } else if ($phone[$i] == '9') {
                $phoneString = $phoneString . 9;
            } else if ($phone[$i] == '0') {
                $phoneString = $phoneString . 0;
            }
        }
        return $phoneString;
    }

    public function firstOrCreateAccountingSoftwareCustomer($accountingSoftware,
                                                            $contractorId,
                                                            $customerName,
                                                            $phone,
                                                            $qbId = null)
    {

        $phone = $this->formatPhone($phone);
        if ($accountingSoftware == 'quickBooks') {
            $qb = new Quickbook();

            // check quickbooks customer table
            if (!empty($qbId)) {
                $qbCustomerData = $qb->getLatestCustomerDataFromQB($qbId, $contractorId);
                $locationId = $this->addLocation($qbCustomerData);
                $user_id = $this->addCustomerToUserTable($qbCustomerData, $locationId, $phone);
                $this->addUserIdToLocationsTable($user_id, $locationId);
                $this->addCustomerToCustomerTable(
                    $qbCustomerData, $locationId, $user_id);
                $this->associateContractorToCustomerTable($user_id, $contractorId, $qbId);
            }
        }

        return User::find($user_id);
    }
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];