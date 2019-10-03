<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'id',
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

    protected $guarded = [];


    public function job()
    {
        $this->hasMany(Job::class, 'location_id', 'id');
    }

    public function jobTask()
    {
        return $this->hasMany(JobTask::class, 'location_id', 'id');
    }
}
