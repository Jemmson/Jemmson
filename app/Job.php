<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    //
    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }
}
