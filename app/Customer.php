<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use mysql_xdevapi\Exception;
use App\Services\RandomPasswordService;
use Illuminate\Support\Facades\Log;
use App\User;

class Customer extends Model
{
    //
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'notes',
        'email_method_of_contact',
        'phone_method_of_contact',
        'sms_method_of_contact',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'customer_id', 'user_id');
    }

    public function contractors()
    {
        return $this->belongsToMany(
            'App\Contractor',
            'contractor_customer',
            'contractor_user_id',
            'customer_user_id'
        );
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    private function updateJoblocations($locationId)
    {
        Job::where('customer_id', $this->user()->first()->id)->update(['location_id' => $locationId]);
    }

    public function updateTaskLocations()
    {
        $jobs = Job::where('customer_id', $this->user()->first()->id)->get();
        foreach ($jobs as $job) {
            $jobTasks = $job->jobTasks()->get();
            foreach ($jobTasks as $jobTask) {
                $jobTask->location_id = $job->location_id;

                try {
                    $jobTask->save();
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'code' => $e->getCode()
                    ], 200);
                }

            }
        }
    }

    public function updateLocation($request)
    {
        $this->timestamps = true;
        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->user()->first()->id;
            $location->default = true;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
            $location->default = true;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();

            if ($request->jobAddressIsDifferent) {
                self::updateJobLocation($request);
            } else {
                $this->updateJobLocations($this->location_id);
                $this->updateTaskLocations();
            }


        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        } finally {
            $this->timestamps = false;
        }


    }

    public function updateJobLocation($request)
    {
        $location = new Location();
        $location->user_id = $this->user()->first()->id;
        $location->default = true;
        $location->address_line_1 = $request->job['address_line_1'];
        $location->address_line_2 = $request->job['address_line_2'];
        $location->city = $request->job['city'];
        $location->area = $request->job['city'];
        $location->state = $request->job['state'];
        $location->zip = $request->job['zip'];

        try {
            $location->save();
            $this->updateJobLocations($location->id);
            $this->updateTaskLocations();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }

    }

    /**
     * This function creates a new user
     *
     * @param string $email the email address of the customer
     * @param string $phone the phone number of the customer
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function createNewCustomer($phone, $customerName, $contractorId, $firstName = '', $lastName = '')
    {

        if (empty($phone) || $phone === '') {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        $customer = null;


        try {
            $customer = User::create(
                [
                    'name' => $customerName,
                    'sms' => 1,
//                    'email' => $email,
                    'phone' => $phone,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'usertype' => 'customer',
                    'password_updated' => false,
                    'password' => bcrypt($pass),
                ]
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            Log::error($exception);
            return ["message" => $exception->getMessage(), "code" => $exception->getCode()];
        }

        if (empty(Customer::select()->where("user_id", "=", $customer->id)->get()->first())) {
            Customer::create(
                [
                    'user_id' => $customer->id
                ]
            );
        }

        $cc = new ContractorCustomer();
        $cc->associateCustomer($contractorId, $customer->id);

        return $customer;

    }

    // TODO: understand where an intermidate table relates to two other tables
    // TODO: define relationship where table references itself
    // TODO: I need to be able to have a contractor reference many contractors


}
