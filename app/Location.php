<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
    public function job()
    {
        $this->belongsTo(Job::class, 'location_id', 'id');
    }
}
