<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quickbook;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class QuickBooksController extends Controller
{
    public $credentials = [];

    public function refreshToken($dataservice)
    {
        // Create SDK instance
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());
        $accessToken = unserialize(session('sessionAccessToken'));
        $dataService->updateOAuth2Token($accessToken);
    }

    public function getCompanyInfo()
    {
        // Create SDK instance
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());
        $accessToken = session('sessionAccessToken');
        $accessToken = unserialize(base64_decode($accessToken));
        $dataService->updateOAuth2Token($accessToken);
        $companyInfo = $dataService->getCompanyInfo();
        $address = "QBO API call Successful!! Response Company name: " .
            $companyInfo->CompanyName . " Company Address: " .
            $companyInfo->CompanyAddr->Line1 . " " .
            $companyInfo->CompanyAddr->City . " " .
            $companyInfo->CompanyAddr->PostalCode;
        return $address;
    }


    public function processCodeAndGetCompany($code, $company_id)
    {
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $company_id);
        $dataService->updateOAuth2Token($accessToken);
        $companyInfo = $dataService->getCompanyInfo();
        return $companyInfo;
    }

    public function processCode($code, $company_id)
    {
        $qb = new Quickbook();
        $dataService = DataService::Configure($qb->getCredentials());
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $company_id);
        $dataService->updateOAuth2Token($accessToken);
        $sessionAccessToken = base64_encode(serialize($accessToken));
        session(['sessionAccessToken' => $sessionAccessToken]);
    }

    public function getAuthURL($state)
    {
        $qb = new Quickbook();
        if (!empty($state)) {
            $qb->setState(['method' => $state]);
            $dataService = DataService::Configure($qb->getCredsWithState());
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        } else {
            $dataService = DataService::Configure($qb->getAuthCredentials());
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        }
        return $authUrl;
    }


    public function getCachedCompanyInfo()
    {
        $companyInfo = session('companyInfo');
        if (!empty($companyInfo)) {
            return response()->json([
                'message' => $companyInfo
            ], 200);
        } else {
            return response()->json([
                'message' => 'no company info'
            ], 200);
        }
    }

    public function processToken(Request $request)
    {
        $location = '';
        if (!empty($request->state)) {
            $location = collect(json_decode($request->state))->toArray()['method'];
        }
        if (empty(Auth::user()->id)) {
            $companyInfo = $this->processCodeAndGetCompany($request->code, $request->realmId);
            session(['companyInfo' => $companyInfo]);
            return Redirect::to('/#/registerQuickBooks');
        } else
            if (empty($q) && !empty(Auth::user()->id)) {
                Quickbook::create([
                    'user_id' => Auth::user()->id,
                    'state' => $request->state,
                    'code' => $request->code,
                    'company_id' => $request->realmId
                ]);
            } else if (!empty($q) && !empty(Auth::user()->id)) {
                $q->state = $request->state;
                $q->code = $request->code;
                $q->company_id = $request->realmId;
                $q->save();
            }
        if ($location == 'getCompany') {
            $companyInfo = $this->processCodeAndGetCompany($request->code, $request->realmId);
            return Redirect::back(302)->with(json_encode($companyInfo));
        } else {
            $this->processCode($request->code, $request->realmId);
            return Redirect::to('/#/home');
        }
    }
}
