<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Job;

class JobActions extends Model
{
    public function job() 
    {
        $this->belongsTo(Job::class);
    }
}
