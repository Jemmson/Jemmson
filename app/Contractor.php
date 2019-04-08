<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Customer;

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

    public function checkIfPhoneMatchesPhoneInQuickbooksCustomerTable($phone)
    {

    }

    public function firstOrCreateAccountingSoftwareCustomer($accountingSoftware, \App\User $customer, $phone, $qbId = null)
    {
//        if ($accountingSoftware == 'quickBooks') {
//            $qb = new Quickbook();
//            if (!empty($qb->checkIfQuickbooksCustomerExists($customer))) {
//                $qb->addCustomer($customer);
//            }
//        }

        if ($accountingSoftware == 'quickBooks') {
            $qb = new Quickbook();

            // check quickbooks customer table
            if (!empty($qbId)) {
                $qbc = QuickbooksCustomer::select()->where('quickbooks_id', '=', $qbId);
                // TODO: pull latest customer data from QB
                $qb->getLatestCustomerDataFromQB($qbId);

                // TODO: save data to User Table and Customer Table and ContractorCustomer Table

            } else if (!empty(QuickbooksCustomer::select()->where('phone', '=', $phone))) {
                // TODO: pull latest customer data from QB

                // TODO: save data to User Table and Customer Table and ContractorCustomer Table

            } else {
                // TODO: add user customer to quickbooks

                // TODO: save data to User Table and Customer Table and ContractorCustomer Table
//                $customer = Customer::createNewCustomer($phone, $customerName);
            }


//            if (!empty($qb->checkIfQuickbooksCustomerExists($customer))) {
            $qb->addCustomer($customer);
//            }
        }
    }
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];