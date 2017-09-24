<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = [
      'user_id',
      'email_method_of_contact',
      'address_line_1',
      'address_line_2',
      'city',
      'state',
      'zip',
      'company_logo_name',
      'sms_method_of_contact',
      'phone_method_of_contact',
      'phone_number',
      'company_name',
    ];

    //
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
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
