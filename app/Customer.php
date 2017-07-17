<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function contractors()
    {
        return $this->belongsToMany(contractor::class);
    }

    // TODO: understand where an intermidate table relates to two other tables
    // TODO: define relationship where table references itself
    // TODO: I need to be able to have a contractor reference many contractors
}
