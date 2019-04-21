<?php

namespace App\Traits\Auth;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Quickbook;
use App\QuickbooksItem;

trait AuthenticateUsers
{

    /**
     * Handle a Authenticates the User.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            return $this->successfulLogin($request);
        }
        return $this->failedLogin($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        //Try with email AND username fields
        if (Auth::attempt([
                'phone' => preg_replace("/[^0-9]/", "", $request['username']),
                'password' => $request['password']
            ], $request->has('remember'))
            || Auth::attempt([
                'email' => $request['username'],
                'password' => $request['password']
            ], $request->has('remember'))) {
            return true;
        }
        return false;
    }

    /**
     * This is executed when the user successfully logs in
     *
     * @var Request $request
     * @return Reponse
     */
    protected function successfulLogin(Request $request)
    {


        $qb = new Quickbook();
        if ($qb->isContractorThatUsesQuickbooks()) {
            if ($qb->contractorSubscriptionIsStillActive()) {
                if ($qb->updateAccessToken()) {
                    $qb->syncCustomerInformationFromQB(Auth::user()->getAuthIdentifier());
                    $qb->syncTasksFromQB(Auth::user()->getAuthIdentifier());
                }
            } else {
                // TODO: Redirect to a page that will say their subscription to QB is no longer active and they should chose whether they want to renew or not
                // TODO: If they want to renew then they will be logged out of the application and then
                // TODO: they will be redirected to the subscription page of QB
                // TODO: If no then they will simply go to the login page
            }
        }

        return response('Success', 200);
    }

    /**
     * This is executed when the user fails to log in
     *
     * @var Request $request
     * @return Reponse
     */
    protected function failedLogin(Request $request)
    {
        return response('Error', 422);
    }

}
