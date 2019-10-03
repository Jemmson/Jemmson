<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Time extends Model
{
    //
    use SoftDeletes;
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
