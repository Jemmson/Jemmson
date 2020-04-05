<?php

namespace Laravel\Spark\Interactions\Settings\Profile;

use Illuminate\Support\Facades\Validator;
use Laravel\Spark\Events\Profile\ContactInformationUpdated;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation as Contract;

class UpdateContactInformation implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function validator($user, array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($user, array $data)
    {
        $user->forceFill([
            'name' => $data['name'],
            'first_name' => $data['fname'],
            'last_name' => $data['lname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'billing_address' => $data['addressline1'],
            'billing_address_line_2' => $data['addressline2'],
            'billing_city' => $data['city'],
            'billing_state' => $data['state'],
            'billing_zip' => $data['zip']
        ])->save();

        event(new ContactInformationUpdated($user));

        return $user;
    }
}
