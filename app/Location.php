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
        'area'
    ];


    public function job()
    {
        $this->belongsTo(Job::class, 'location_id', 'id');
    }
}
