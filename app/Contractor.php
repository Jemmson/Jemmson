<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    // no empty comments
    public function user()
    {
        //fix this here
        return $this->belongsTo(User::class);
    }

    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
}
