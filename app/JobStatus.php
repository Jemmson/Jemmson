<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class JobStatus extends Model
{
    protected $table = 'job_status';
    public $timestamps = false;
    protected $fillable = [
        'job_id',
        'status_number',
        'status'
    ];
    use SoftDeletes;


    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function setStatus($jobId, $status)
    {

        $js = JobStatus::where('job_id', '=', $jobId)
            ->orderBy('created_at', 'desc')
            ->get()->first();

        if (empty($this->checkStatus($jobId, $status))
            || $js['status'] != $status ) {
            $statusNumber = $this->getStatusNumber($status);
            $this->fill([
                'job_id' => $jobId,
                'status_number' => $statusNumber,
                'status' => $status
            ]);
            $this->save();
        } else {
            $date = Carbon::now();
            $js->sent_on = $date->format('yy-m-d h:m:s');
            $js->save();
        }
    }

    private function checkStatus($job_id, $status)
    {
        return JobStatus::where("status", "=", $status)
            ->where("job_id", "=", $job_id)->get()->first();
    }


    public function getStatusNumber($status)
    {
        switch ($status){
            case 'initiated':
                return 1;
                break;
            case 'in_progress':
                return 2;
                break;
            case 'sent':
                return 3;
                break;
            case 'changed':
                return 4;
                break;
            case 'canceled_by_customer':
                return 5;
                break;
            case 'canceled_by_general':
                return 6;
                break;
            case 'approved':
                return 7;
                break;
            case 'declines_finished_task':
                return 8;
                break;
            case 'paid':
                return 9;
                break;
        }
    }
}
