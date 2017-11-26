<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    //
//    public function time()
//    {
//        return $this->hasMany(Time::class);
//    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')
            ->withPivot(
                'cust_final_price',
                'sub_final_price',
                'sub_cont_proposed',
                'cont_sub_proposed',
                'cust_cont_proposed',
                'cont_cust_proposed',
                'sub_cont_accepted',
                'cont_sub_accepted',
                'cust_cont_accepted',
                'cont_cust_accepted'
            )
            ->withTimestamps();
    }
}
