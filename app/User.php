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
     * Save customer Stripe id
     *
     * @param String $id
     * @return Boolean
     */
    public function saveStripeId($id)
    {
        if ($id === null) {
            return false;
        }

        $this->stripe_id = $id;

        try {
            $this->save();
        } catch (\Excpetion $e) {
            Log::error('Saving Stripe Id: ' . $e-getMessage());
            return false;
        }

        return true;
    }

    /**
     * Save card infromation from stripe
     *
     * @param [type] $card
     * @return Boolean
     */
    public function saveCardInformation($card)
    {
        if ($card === null) {
            return false;
        }

        $this->card_brand = $card['brand'];
        $this->card_last_four = $card['last4'];
        $this->card_country = $card['country'];

        try {
            $this->save();
        } catch (\Excpetion $e) {
            Log::error('Saving Stripe Id: ' . $e-getMessage());
            return false;
        }

        return true;
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
     * @return [obj]
     */
    public function getDetails()
    {
        if ($this->usertype === 'contractor') {
            return $this->contractor()->first();
        } else {
            return $this->customer()->first();
        }

    }

    /**
     * Are sms notifications on for this user
     *
     * @return bool
     */
    public function smsOn()
    {
        if ($this->usertype === 'contractor') {
            $on = $this->contractor()->first()->sms_method_of_contact;
        } else {
            $on = $this->customer()->first()->sms_method_of_contact;
        }

        return $on;
    }

    /**
     * Are email notifications on for this user
     *
     * @return bool
     */
    public function emailOn()
    {
        if ($this->usertype === 'contractor') {
            $on = $this->contractor()->first()->email_method_of_contact;
        } else {
            $on = $this->customer()->first()->email_method_of_contact;
        }
        return $on;
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

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        // NOTICE: only handling US phone numbers
        // this along with phone validation will need to be updated to handle other country phone numbers
        return '1' . $this->phone;
    }

}
