<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Spark\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Traits\Utilities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$router->group(['middleware' => 'web'], function ($router) {
    $teamString = Spark::teamString();

    $pluralTeamString = str_plural(Spark::teamString());

    // Terms Of Service...
    $router->get('/terms', 'TermsController@show')->name('terms');

    // Missing Team Notice...
    $router->get('/missing-'.$teamString, 'MissingTeamController@show');

    // Customer Support...
    $router->post('/support/email', 'SupportController@sendEmail');

    // API Token Refresh...
    $router->put('/spark/token', 'TokenController@refresh');

    // Users...
    $router->get('/user/current', 'UserController@current');
    $router->put('/user/last-read-announcements-at', 'UserController@updateLastReadAnnouncementsTimestamp');

    // Notifications
    $router->get('/notifications/recent', 'NotificationController@recent');
    $router->put('/notifications/read', 'NotificationController@markAsRead');

    // Settings Dashboard...
    $router->get('/settings', 'Settings\DashboardController@show')->name('settings');

    // Profile Contact Information...
    $router->put('/settings/contact', 'Settings\Profile\ContactInformationController@update');

    // Profile Photo...
    $router->post('/settings/photo', 'Settings\Profile\PhotoController@uploadTaskImage');
//    $router->post('/settings/photo', 'Settings\Profile\PhotoController@store');

    // Teams...
    if (Spark::usesTeams()) {
        // General Settings...
        $router->get('/settings/'.$pluralTeamString.'/roles', 'Settings\Teams\TeamMemberRoleController@all');
        $router->get('/settings/'.$pluralTeamString.'/{team}', 'Settings\Teams\DashboardController@show')->name('settings.team');

        $router->get('/'.$pluralTeamString.'', 'TeamController@all');
        $router->get('/'.$pluralTeamString.'/current', 'TeamController@current');
        $router->get('/'.$pluralTeamString.'/{team_id}', 'TeamController@show');
        $router->post('/settings/'.$pluralTeamString, 'Settings\Teams\TeamController@store');
        $router->post('/settings/'.$pluralTeamString.'/{team}/photo', 'Settings\Teams\TeamPhotoController@update');
        $router->put('/settings/'.$pluralTeamString.'/{team}/name', 'Settings\Teams\TeamNameController@update');

        // Invitations...
        $router->get('/settings/'.$pluralTeamString.'/{team}/invitations', 'Settings\Teams\MailedInvitationController@all');
        $router->post('/settings/'.$pluralTeamString.'/{team}/invitations', 'Settings\Teams\MailedInvitationController@store');
        $router->get('/settings/invitations/pending', 'Settings\Teams\PendingInvitationController@all');
        $router->get('/invitations/{invitation}', 'InvitationController@show');
        $router->post('/settings/invitations/{invitation}/accept', 'Settings\Teams\PendingInvitationController@accept');
        $router->post('/settings/invitations/{invitation}/reject', 'Settings\Teams\PendingInvitationController@reject');
        $router->delete('/settings/invitations/{invitation}', 'Settings\Teams\MailedInvitationController@destroy');

        $router->put('/settings/'.$pluralTeamString.'/{team}/members/{team_member}', 'Settings\Teams\TeamMemberController@update');
        $router->delete('/settings/'.$pluralTeamString.'/{team}/members/{team_member}', 'Settings\Teams\TeamMemberController@destroy');
        $router->delete('/settings/'.$pluralTeamString.'/{team}', 'Settings\Teams\TeamController@destroy');
        $router->get('/'.$pluralTeamString.'/{team}/switch', 'TeamController@switchCurrentTeam');

        // Billing

        // Subscription Settings...
        $router->post('/settings/'.$pluralTeamString.'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@store');
        $router->put('/settings/'.$pluralTeamString.'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@update');
        $router->delete('/settings/'.$pluralTeamString.'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@destroy');

        // VAT ID Settings...
        $router->put('/settings/'.$pluralTeamString.'/{team}/payment-method/vat-id', 'Settings\Teams\PaymentMethod\VatIdController@update');

        // Credit Card Settings...
        $router->put('/settings/'.$pluralTeamString.'/{team}/payment-method', 'Settings\Teams\PaymentMethod\PaymentMethodController@update');

        // Redeem Coupon...
        $router->post('/settings/'.$pluralTeamString.'/{team}/payment-method/coupon', 'Settings\Teams\PaymentMethod\RedeemCouponController@redeem');

        // Billing History...
        $router->put(
            '/settings/'.$pluralTeamString.'/{team}/extra-billing-information',
            'Settings\Teams\Billing\BillingInformationController@update'
        );

        // Coupons...
        $router->get('/coupon/'.$teamString.'/{id}', 'TeamCouponController@current');

        // Invoices...
        $router->get('/settings/'.$pluralTeamString.'/{team}/invoices', 'Settings\Teams\Billing\InvoiceController@all');
        $router->get('/settings/'.$pluralTeamString.'/{team}/invoice/{id}', 'Settings\Teams\Billing\InvoiceController@download');
    }

    // Security Settings...
    $router->put('/settings/password', 'Settings\Security\PasswordController@update');
    $router->post('/settings/two-factor-auth', 'Settings\Security\TwoFactorAuthController@enable');
    $router->delete('/settings/two-factor-auth', 'Settings\Security\TwoFactorAuthController@disable');

    // API Settings
    $router->get('/settings/api/tokens', 'Settings\API\TokenController@all');
    $router->post('/settings/api/token', 'Settings\API\TokenController@store');
    $router->put('/settings/api/token/{token_id}', 'Settings\API\TokenController@update');
    $router->get('/settings/api/token/abilities', 'Settings\API\TokenAbilitiesController@all');
    $router->delete('/settings/api/token/{token_id}', 'Settings\API\TokenController@destroy');

    // Plans...
    $router->get('/spark/plans', 'PlanController@all');

    // Subscription Settings...
    $router->post('/settings/subscription', 'Settings\Subscription\PlanController@store');
    $router->put('/settings/subscription', 'Settings\Subscription\PlanController@update');
    $router->delete('/settings/subscription', 'Settings\Subscription\PlanController@destroy');

    // VAT ID Settings...
    $router->put('/settings/payment-method/vat-id', 'Settings\PaymentMethod\VatIdController@update');

    // Credit Card Settings...
    $router->put('/settings/payment-method', 'Settings\PaymentMethod\PaymentMethodController@update');

    // Redeem Coupon...
    $router->post('/settings/payment-method/coupon', 'Settings\PaymentMethod\RedeemCouponController@redeem');

    // Billing History...
    $router->put(
        '/settings/extra-billing-information',
        'Settings\Billing\BillingInformationController@update'
    );

    // Invoices...
    $router->get('/settings/invoices', 'Settings\Billing\InvoiceController@all');
    $router->get('/settings/invoice/{id}', 'Settings\Billing\InvoiceController@download');

    // Coupons...
    $router->get('/coupon/user/{id}', 'CouponController@current');
    $router->get('/coupon/{code}', 'CouponController@show');

    // Kiosk...
    $router->get('/spark/kiosk', 'Kiosk\DashboardController@show')->name('kiosk');

    // Kiosk Search...
    $router->post('/spark/kiosk/users/search', 'Kiosk\SearchController@performBasicSearch');

    // Kiosk Announcements...
    $router->get('/spark/kiosk/announcements', 'Kiosk\AnnouncementController@all');
    $router->post('/spark/kiosk/announcements', 'Kiosk\AnnouncementController@store');
    $router->put('/spark/kiosk/announcements/{id}', 'Kiosk\AnnouncementController@update');
    $router->delete('/spark/kiosk/announcements/{id}', 'Kiosk\AnnouncementController@destroy');

    // Kiosk Metrics / Performance Indicators...
    $router->get('/spark/kiosk/performance-indicators', 'Kiosk\PerformanceIndicatorsController@all');
    $router->get('/spark/kiosk/performance-indicators/revenue', 'Kiosk\PerformanceIndicatorsController@revenue');
    $router->get('/spark/kiosk/performance-indicators/plans', 'Kiosk\PerformanceIndicatorsController@subscribers');
    $router->get('/spark/kiosk/performance-indicators/trialing', 'Kiosk\PerformanceIndicatorsController@trials');

    // Kiosk User Profiles...
    $router->get('/spark/kiosk/users/{id}/profile', 'Kiosk\ProfileController@show');

    // Kiosk Discounts...
    $router->post('/spark/kiosk/users/discount/{id}', 'Kiosk\DiscountController@store');

    // Kiosk Impersonation...
    $router->get('/spark/kiosk/users/impersonate/{id}', 'Kiosk\ImpersonationController@impersonate');
    $router->get('/spark/kiosk/users/stop-impersonating', 'Kiosk\ImpersonationController@stopImpersonating');

    // Authentication...
    $router->get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    $router->post('/login', 'Auth\LoginController@login');
    $router->get('/logout', 'Auth\LoginController@logout')->name('logout');

    // Two-Factor Authentication Routes...
    $router->get('/login/token', 'Auth\LoginController@showTokenForm');
    $router->post('/login/token', 'Auth\LoginController@verifyToken');

    // Two-Factor Emergency Token Login Routes...
    $router->get('/login-via-emergency-token', 'Auth\EmergencyLoginController@showLoginForm');
    $router->post('/login-via-emergency-token', 'Auth\EmergencyLoginController@login');

    // Registration...
    $router->get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    $router->post('/register', 'Auth\RegisterController@register');

    $router->post('/registerContractor', function (Request $request) {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'terms' => 'required',
                'companyName' => 'required',
                'phoneNumber' => 'required',
                'addressLine1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'country' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }


        $user = new User();
        $user->name = $request->first_name . " " . $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->usertype;
        $user->password = bcrypt($request->password);
        $user->phone = Utilities::digitsOnlyStatic($request->phone);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = Utilities::digitsOnlyStatic($request->phoneNumber);
        $user->billing_address = $request->addressLine1;
        $user->billing_address_line_2 = $request->addressLine2;
        $user->billing_city = $request->city;
        $user->billing_state = $request->state;
        $user->billing_zip = $request->zip;
        $user->billing_country = $request->country;
        $user->password_updated = true;
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        $location = new \App\Location();
        $location->user_id = $user->id;
        $location->address_line_1 = $request->addressLine1;
        $location->address_line_2 = $request->addressLine2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;
        $location->country = $request->country;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $contractor = new \App\Contractor();
        $contractor->user_id = $user->id;
        $contractor->location_id = $location->id;
        $contractor->company_name = $request->companyName;

        try {
            $contractor->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $user->location_id = $location->id;

        foreach($request->licenses as $license){
            $l = new \App\License();
            $l->contractor_id = $user->id;
            $l->name = $license['name'];
            $l->value = $license['value'];

            try {
                $l->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }

        }

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        Auth::loginUsingId($user->id);

//        $userData = User::where('id', '=', $user->id)->select('name','first_name','last_name')->get()->first();
        $userData = $user->select(
            'name',
            'email',
            'usertype',
            'phone',
            'first_name',
            'last_name',
            'billing_address',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_zip',
            'billing_country',
            'password_updated',
            'id',
            'location_id'
        )->get()->first();

        return response()->json([
            'redirect' => '/#/home',
            'user' => $userData
        ]);
    });
//    $router->post('/registerContractor', 'Auth\RegisterController@registerContractor');
    $router->post('/registerCustomer', function (Request $request) {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'terms' => 'required',
            'phoneNumber' => 'required',
            'addressLine1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user = new User();
        $user->name = $request->first_name . " " . $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->usertype;
        $user->password = bcrypt($request->password);
        $user->phone = Utilities::digitsOnlyStatic($request->phone);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = Utilities::digitsOnlyStatic($request->phoneNumber);
        $user->billing_address = $request->addressLine1;
        $user->billing_address_line_2 = $request->addressLine2;
        $user->billing_city = $request->city;
        $user->billing_state = $request->state;
        $user->billing_zip = $request->zip;
        $user->billing_country = $request->country;
        $user->password_updated = true;
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        $location = new \App\Location();
        $location->user_id = $user->id;
        $location->address_line_1 = $request->addressLine1;
        $location->address_line_2 = $request->addressLine2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;
        $location->country = $request->country;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }


        $customer = new \App\Customer();
        $customer->user_id = $user->id;
        $customer->location_id = $location->id;

        try {
            $customer->save();
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

        Auth::loginUsingId($user->id);

        $userData = DB::select(['name','first_name','last_name'])->where('id','=',$user->id)->get()->first();

        return response()->json([
            'redirect' => '/#/home',
            'user' => $userData
        ]);

    });


    $router->post('/registerUser', 'Auth\RegisterController@registerUser');


    // Password Reset...
    $router->get('/password/reset/{token?}', 'Auth\PasswordController@showResetForm')->name('password.reset');
    $router->post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');
    $router->post('/password/reset', 'Auth\PasswordController@reset');
});

// Tax Rates...
$router->post('/tax-rate', 'TaxRateController@calculate');

// Geocoding...
$router->get('/geocode/country', 'GeocodingController@country');
$router->get('/geocode/states/{country}', 'GeocodingController@states');

// Webhooks...
$router->post('/webhook/stripe', 'Settings\Billing\StripeWebhookController@handleWebhook');
$router->post('/webhook/braintree', 'Settings\Billing\BraintreeWebhookController@handleWebhook');
