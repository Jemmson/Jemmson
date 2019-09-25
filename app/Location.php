<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //

    protected $fillable = [
        'user_id',
        'default',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'area',
        'country',
        'lat',
        'long'
    ];


    public function job()
    {
        $this->hasMany(Job::class, 'location_id', 'id');
    }

    public function jobTask()
    {
        return $this->hasMany(JobTask::class, 'location_id', 'id');
    }
}
