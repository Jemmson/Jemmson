<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    //

    protected $table = 'task_messages';
    protected $guarded = [];

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }



}
