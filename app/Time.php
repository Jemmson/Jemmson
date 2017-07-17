<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    //
    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function tasks()
    {
        return $this->belongsTo(task::class);
    }
}
