<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contractor extends Model
{
    protected $fillable = [
        'user_id',
        'email_method_of_contact',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'company_logo_name',
        'sms_method_of_contact',
        'phone_method_of_contact',
        'phone_number',
        'company_name',
    ];

    //
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withPivot('base_price')
            ->withTimestamps();
    }

    public function addContractorToBidForJobTable($contractorId, $taskId, $jobId)
    {

        DB::table('bid_contractor_job_task')->insert(
            ['contractor_id' => $contractorId, 'task_id' => $taskId, "job_id" => $jobId]
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
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];