<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobTaskStatus extends Model
{
    //
    protected $table = 'job_task_status';
    protected $guarded = [];

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function setStatus($job_task_id, $status)
    {

        $jts = JobTaskStatus::where('job_task_id', '=', $job_task_id)
            ->orderBy('created_at', 'desc')
            ->get()->first();

        if (empty($this->checkStatus($job_task_id, $status)) || $jts['status'] != $status ) {
            $statusNumber = $this->getStatusNumber($status);
            $this->fill([
                'job_task_id' => $job_task_id,
                'status_number' => $statusNumber,
                'status' => $status
            ]);
            $this->save();
        } else {
            $date = Carbon::now();
            $jts->sent_on = $date->format('yy-m-d h:m:s');
            $jts->save();
        }
    }

    private function checkStatus($job_task_id, $status)
    {
        return JobTaskStatus::where("status", "=", $status)
            ->where("job_task_id", "=", $job_task_id)->get()->first();
    }


    public function getStatusNumber($status)
    {
        switch ($status){
            case 'initiated':
                return 1;
                break;
            case 'waiting_for_customer_approval':
                return 2;
                break;
            case 'customer_changes_bid':
                return 3;
                break;
            case 'canceled_by_customer':
                return 4;
                break;
            case 'canceled_by_general':
                return 5;
                break;
            case 'approved_by_customer':
                return 6;
                break;
            case 'general_finished_work':
                return 7;
                break;
            case 'sub_finished_work':
                return 8;
                break;
            case 'declined_subs_work':
                return 9;
                break;
            case 'approved_subs_work':
                return 10;
                break;
            case 'customer_changes_finished_task':
                return 11;
                break;
            case 'paid':
                return 12;
                break;
        }
    }

    public static function getLastStatus($jobTaskStatus)
    {
        return $jobTaskStatus[count($jobTaskStatus) - 1]->status;
    }

}
