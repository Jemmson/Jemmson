<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Log;

class Task extends Model
{
    protected $fillable = [
        'name',
        'standard_task_id',
        'contractor_id',
        'proposed_cust_price',
        'average_cust_price',
        'proposed_sub_price',
        'average_sub_price',
        'job_id'
    ];
    //
//    public function time()
//    {
//        return $this->hasMany(Time::class);
//    }
//
//    public function standard_task()
//    {
//        return $this->belongsTo(StandardTask::class);
//    }
//
//    public function contractors()
//    {
//        return $this->belongsToMany(Contractor::class);
//    }

    public function Jobs()
    {
        return $this->belongsToMany('App\Job')
            ->withPivot(
                'contractor_id',
                'status',
                'cust_final_price',
                'sub_final_price'
            )
            ->withTimestamps();
    }

    public function Contractors()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(
                'base_price'
            )
            ->with('contractor')
            ->withTimestamps();
    }

    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class);
    }

    public function jobTask()
    {
        return $this->hasOne(JobTask::class);
    }

    public function updateStatus($status)
    {
        $jobTask = $this->jobTask()->first();
        $jobTask->status = $status;

        try {
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Update Job Task: ' . $e->getMessage());
        }
    }

    public function location()
    {
        return $this->hasOne(Location::class);
    }

    public function updateLocation($request)
    {
        if ($this->location_id === null) {
            $location = new Location();
            $location->task_id = $this->id;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        }
        
        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch(\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }
    }

    public static function getBidPrices($jobId)
    {
//         $bidPrices = DB::select("select 
//                         bid_contractor_job_task.id,
//                         bid_contractor_job_task.contractor_id, 
//                         bid_contractor_job_task.task_id, 
//                         bid_contractor_job_task.bid_price,
//                       from 
//                         contractors 
//                       inner join 
//                         bid_contractor_job_task 
//                       on 
//                         bid_contractor_job_task.contractor_id=contractors.id 
//                       where 
//                         bid_contractor_job_task.job_id=$jobId");

//         $contractorNames = [];
// //
//         foreach ($bidPrices as $bidPrice) {
//             $contractorName = DB::select("select
//                         users.name
//                         from users
//                         inner join contractors
//                         on users.id = contractors.user_id
//                         where contractors.id = $bidPrice->contractor_id");
//             array_push($contractorNames, $contractorName);
//         }

//         $bidPriceLength = sizeof($bidPrices);
//         for ($i = 0; $i < $bidPriceLength; $i++) {
//             $bidPrices[$i]->contractorName = $contractorNames[$i];
//         }

//         $bidPrices = json_encode($bidPrices);

//        return $bidPrices;

    }
}
