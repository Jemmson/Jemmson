<?php

namespace App;

use Laravel\Spark\User as SparkUser;
use Illuminate\Notifications\Notifiable;

class User extends SparkUser
{
    use Traits\Passwordless;
    use Notifiable;
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
        'usertype',
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
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information'
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

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function contractor()
    {
        return $this->hasOne(Contractor::class);
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
     * Get all jobs this user is associated with
     *
     * @return void
     */
    public function jobs()
    {
        if ($this->usertype === 'contractor') {
            return $this->hasMany(Job::class, 'contractor_id');
        } else {
            return $this->hasMany(Job::class, 'customer_id');
        }
    }

    /**
     * Get more details about this user
     * whether they are a contractor or customer
     *
     * Notice: We are assuming the correct details for this user
     * is the first record found TODO: is there a better a way to do this?
     *
     * @return [obj]
     */
    public function getDetails()
    {
        if ($this->usertype === 'contractor') {
            return $this->contractor()->first() != null ? $this->contractor()->first() : null;
        } else {
            //dd($this->customers()->first());
            return $this->customer()->first() != null ? $this->customer()->first() : null;
        }

    }

    /**
     * Update user password
     *
     * @param [string] $password a password
     *
     * @return void
     */
    public function updatePassword($password)
    {
        try {
            $this->password = bcrypt($password);
            $this->password_updated = true;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Model User: ' . $e->getMessage());
        }
    }

    public function isCustomer()
    {
        return $this->usertype === 'customer';
    }
}
