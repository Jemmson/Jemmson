<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Job;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobActions extends Model
{
    use SoftDeletes;
    public function job() 
    {
        $this->belongsTo(Job::class);
    }
}
