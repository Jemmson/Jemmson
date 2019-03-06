<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quickbook;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

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

    public function updateCompanyInfo()
    {
//        functionality does not yet exist
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
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        if (!empty($state)) {
            $qb->setState(['method' => $state, 'guid' => $guid]);
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

        // check if guid is valid to protect against csrf requests
        // if it is then get the access token
        $qb = new Quickbook();
        $guid = collect(json_decode($request->state))->toArray()['guid'];
        if ($qb->checkIfGuidIsValid($guid)) {
            $qb->getAccessTokenFromCompanyId($request->code, $request->realmId);
            $qb->getTheCompanyInfo();
            return Redirect::to('/#/registerQuickBooks');

        } else {
            return Redirect::to('/#/check_accounting');
        }
    }
}
