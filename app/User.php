<?php

namespace App;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    use Traits\Passwordless;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password_updated',
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
        'usertype',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }

    public function contractors()
    {
        return $this->hasMany(Contractor::class, 'user_id', 'id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function elements()
    {
        return $this->belongsTo(Element::class);
    }

    /**
     * Get more details about this user
     * whether they are a contractor or customer
     * 
     * Notice: We are assuming the correct details for this user 
     * is the first record found TODO: is there a better
     * way to do this?
     *
     * @return [obj] 
     */
    public function getDetails() 
    {
        if ($this->usertype === 'contractor') {
            return $this->contractors()->first() != null ? $this->contractors()->first() : null;
        } else {
            //dd($this->customers()->first());
            return $this->customers()->first() != null ? $this->customers()->first() : null;
        }

    }
}
