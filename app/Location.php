<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    //
    use SoftDeletes;
    public $timestamps = false;

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

    public function addNewLocation($address_line_1, $address_line_2, $city, $state, $zip, $userId)
    {
        $this->timestamps = true;
        $this->user_id = $userId;
        $this->address_line_1 = $address_line_1;
        $this->address_line_2 = $address_line_2;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->area = $city;
        $this->country = 'US';

        $this->save();

        $this->timestamps = false;

        return $this;
    }

    public function getAddressLine1Attribute($value)
    {
        return strtolower($value);
    }

    public function setAddressLine1Attribute($value)
    {
        $this->attributes['address_line_1'] = strtolower($value);
    }

    public function getCityAttribute($value)
    {
        return strtolower($value);
    }

    public function setCityAttribute($value)
    {
        $this->attributes['city'] = strtolower($value);
    }

    public function getStateAttribute($value)
    {
        return strtolower($value);
    }

    public function setStateAttribute($value)
    {
        $this->attributes['state'] = strtolower($value);
    }

    public function getZipAttribute($value)
    {
        return strtolower($value);
    }

    public function setZipAttribute($value)
    {
        $this->attributes['zip'] = strtolower($value);
    }
}
