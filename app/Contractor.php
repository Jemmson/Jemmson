<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contractor extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo_name',
        'email_method_of_contact',
        'sms_method_of_contact',
        'phone_method_of_contact',
    ];

    //
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'contractor_id', 'user_id');
    }

    public function time()
    {
        return $this->hasMany(Time::class, 'contractor_id', 'user_id');
    }


    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function stripeExpress()
    {
        return $this->hasOne(StripeExpress::class, 'contractor_id', 'user_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'contractor_id', 'user_id')->withPivot('base_price')
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

    public function addContractorToBidForJobTable($contractorId, $taskId, $jobId)
    {
        $proposedBidPrice = Task::find($taskId)->proposed_sub_price;
        // TODO: update this
        DB::table('bid_contractor_job_task')->insert(
            ['contractor_id' => $contractorId, 'task_id' => $taskId, "job_id" => $jobId, "bid_price" => $proposedBidPrice]
        );

    }

    public function checkIfContractorSetBidForATask($contractorId, $taskId, $jobId)
    {
        if (empty(DB::table('bid_contractor_job_task')
            ->select('task_id')
            ->where('contractor_id', '=', $contractorId)
            ->where('task_id', '=', $taskId)
            ->where('job_id', '=', $jobId)
            ->get()[0])) {
            return true;
        } else {
            return false;
        }
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'user_id', 'user_id')->where('default', '=', 1);
    }

    public function updateLocation($request)
    {

        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->id;
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
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];