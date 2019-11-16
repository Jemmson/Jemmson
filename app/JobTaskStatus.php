<?php

namespace App;

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

}
