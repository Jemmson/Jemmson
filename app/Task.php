<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function standard_task()
    {
        return $this->belongsTo(StandardTask::class);
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(task::class);
    }
}
