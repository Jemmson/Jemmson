<?php

namespace App;

use App\Services\SanatizeService;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\User as SparkUser;
use Illuminate\Notifications\Notifiable;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Notifications\Messages\NexmoMessage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Doctrine\DBAL\Driver\PDOException;


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
        'usertype',
        'password',
        'password_updated'
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

    protected $userData = [];

//    public function __construct($userData, array $attributes = [])
//    {
//        parent::__construct($attributes);
//        $this->userData = $userData;
//    }
//
//    public function add()
//    {
//        $this->fill($this->userData);
//        $this->save();
//    }

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

    public function checkIfUserExistsElseCreateUser($data)
    {
        $this->fill($data);
        try {
            $this->save();
            return true;
        } catch (\Exception $e) {
            Log::error('Creating A New User ' . $e->getMessage());
            return false;
        }
    }

    public static function checkIfUserExistsByPhoneNumber($phone)
    {
        return User::select()->where('phone', '=', $phone)->get()->first();
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
        } catch (\Exception $e) {
            Log::error('Saving Stripe Id: ' . $e->getMessage());
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
            Log::error('Saving Stripe Id: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Delete card infromation
     *
     * @param [type] $card
     * @return Boolean
     */
    public function deleteCard()
    {
        $this->card_brand = '';
        $this->card_last_four = '';
        $this->card_country = '';
        $this->stripe_id = null;

        try {
            $this->save();
        } catch (\Excpetion $e) {
            Log::error('Deleting Card: ' . $e->getMessage());
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

    static public function validatePhoneNumber($number)
    {

        if (!empty($number)) {
            $number = '1' . $number;
            $client = new Client();

            $res = $client->request('POST', 'https://api.nexmo.com/ni/advanced/json', [
                'json' => [
                    'api_key' => env('NEXMO_KEY'),
                    'api_secret' => env('NEXMO_SECRET'),
                    'number' => $number,
                ]
            ]);


            $response = json_decode($res->getBody());


            if ($response->current_carrier->network_type == 'landline' ||
                $response->original_carrier->network_type == 'landline') {
                return ['failure', $response->original_carrier->network_type, $response->current_carrier->network_type];
            } else {
                return ['success', $response->original_carrier->network_type, $response->current_carrier->network_type];
            }
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

    public static function getCustomersInUserTableByName($name)
    {
        return User::where('name', 'like', '%' . $name . '%')
            ->where('usertype', '!=', 'contractor')
            ->get();
    }

    public static function isCustomerAssociatedWithContractor($customer_id, $contractor_id)
    {

    }

    public static function getUserByPhoneOrEmail($phone, $email)
    {
        return User::where('phone', $phone)->orWhere('email', $email)->first();
    }

    public function updatePhoneNumber($newPhone)
    {

        $this->phone = $newPhone;
        try {
            $this->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

    }

    private function addContractorUser($request)
    {

        $phone = SanatizeService::phone($request->phone);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->phone = $phone;
        $user->password_updated = 0;
        $user->password = bcrypt('random'.rand(0,9999));
        $user->usertype = 'contractor';
        !empty($request->firstName) ? $user->first_name = $request->firstName  :  $user->first_name = $request->givenName;
        !empty($request->lastName) ? $user->last_name = $request->lastName  :  $user->last_name = $request->familyName;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $user;
    }

    private function addUserToContractorTable($request, $user)
    {
        $contractor = new Contractor();
        $contractor->user_id = $user->id;
        $contractor->free_jobs = '5';
        $contractor->company_name = $request->companyName;

        try {
            $contractor->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $contractor;
    }

    private function addGeneralAndSubToContractorContractorTable($request, $user)
    {
        $cc = new ContractorContractor();
        $cc->contractor_id = Auth::user()->getAuthIdentifier();
        $cc->subcontractor_id = $user->id;
        $cc->quickbooks_id = $request->quickbooksId;

        try {
            $cc->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $cc;
    }

    private function addLocationToContractorFromQuickbooksContractorTable($request, $contractor, $user)
    {
        $qbContractor = QuickbooksContractor::where('contractor_id', '=', Auth::user()->getAuthIdentifier())->
            where('quickbooks_id', '=', $request->quickbooksId)->get()->first();
        $location = new Location();
        $location->address_line_1 = $qbContractor->line1;
        $location->address_line_2 = $qbContractor->line2;
        $location->city = $qbContractor->city;
        $location->state = $qbContractor->state;
        $location->zip = $qbContractor->postal_code;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $contractor->location_id = $location->id;

        try {
            $contractor->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $user->location_id = $location->id;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $location;
    }

    public function addContractorToJemTable($request)
    {
      $user = $this->addContractorUser($request);
      $contractor = $this->addUserToContractorTable($request, $user);
      $this->addGeneralAndSubToContractorContractorTable($request, $user);
      $this->addLocationToContractorFromQuickbooksContractorTable($request, $contractor, $user);
      return $user;
    }

}
