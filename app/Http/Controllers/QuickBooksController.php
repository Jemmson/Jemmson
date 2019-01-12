<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quickbook;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class QuickBooksController extends Controller
{
    //

    public $credentials = [];

    public function getCompanyInfo()
    {

        // Create SDK instance
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());

        /*
         * Retrieve the accessToken value from session variable
         */
        $company_id = Quickbook::select('company_id')->
                            where('user_id', '=', Auth::user()->id)->
                            first()->company_id;

        $code = Quickbook::select('code')->
        where('user_id', '=', Auth::user()->id)->
        first()->code;

//        dd($code);


        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $company_id);


        /*
         * Update the OAuth2Token of the dataService object
         */
        $dataService->updateOAuth2Token($accessToken);
        $companyInfo = $dataService->getCompanyInfo();

        dd($companyInfo);

        print_r($companyInfo);
        return $companyInfo;
    }

    public function getAccessToken($code, $company_id)
    {
        $qb = new Quickbook();
        $qb->addCredentials([
            'QBORealmID' => $company_id
        ]);

        $dataService = DataService::Configure($qb->getCredentials());
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $company_id);

        $dataService->updateOAuth2Token($accessToken);
        $q = Quickbook::select()->where('user_id', '=', Auth::user()->id)->first();
        $q->access_token = $accessToken->getAccessToken();
        $q->save();

    }

    public function getAuthURL()
    {
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        return $authUrl;
    }

    public function processToken(Request $request)
    {
        $q = Quickbook::select('user_id')->where('user_id', '=', Auth::user()->id)->first();
        if (empty($q)) {
            Quickbook::create([
                'user_id' => Auth::user()->id,
                'state' => $request->state,
                'code' => $request->code,
                'company_id' => $request->realmId
            ]);
        } else {
            $q->state = $request->state;
            $q->code = $request->code;
            $q->company_id = $request->realmId;
            $q->save();
        }

        $this->getAccessToken($request->code, $request->realmId);

        return Redirect::to('/#/home');
    }

}
