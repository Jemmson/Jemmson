<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'user_id',
        'email_method_of_contact',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'notes',
        'phone_method_of_contact',
        'sms_method_of_contact',
        'phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
    
    public function contractors()
    {
        return $this->belongsToMany(contractor::class);
    }

    // TODO: understand where an intermidate table relates to two other tables
    // TODO: define relationship where table references itself
    // TODO: I need to be able to have a contractor reference many contractors
}
