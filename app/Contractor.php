<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    //
    public function user()
    {
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
