<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'standard_task_id'
    ];
    //
//    public function time()
//    {
//        return $this->hasMany(Time::class);
//    }
//
//    public function standard_task()
//    {
//        return $this->belongsTo(StandardTask::class);
//    }
//
//    public function contractors()
//    {
//        return $this->belongsToMany(Contractor::class);
//    }

    public function Jobs()
    {
        return $this->belongsToMany('App\Job')
            ->withPivot(
                'cust_final_price',
                'sub_final_price'
            )
            ->withTimestamps();
    }

    public function Contractors()
    {
        return $this->belongsToMany('App\Contractor')
            ->withPivot(
                'base_price'
            )
            ->withTimestamps();
    }
}
