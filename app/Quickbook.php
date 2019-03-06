<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use QuickBooksOnline\API\DataService\DataService;

class Quickbook extends Model
{
    //
    protected $fillable = [
        'access_token',
        'code',
        'refresh_token',
        'refresh_token_expires_at',
        'refresh_token_validation_period',
        'company_id',
        'realmId',
        'state',
        'user_id',
    ];

    protected $auth_mode;
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $scope;
    protected $state;
    protected $base_url;
    protected $credentials = [];

    public function addCredentials($newCredentials)
    {
        $nd = collect($newCredentials);
        $creds = collect($this->credentials);
        foreach ($nd as $k => $c) {
            $creds->put($k, $c);
        }
        $this->credentials = $creds->toArray();
    }

    public function getAccessTokenFromCompanyId($code, $companyId)
    {
        $dataService = DataService::Configure($this->getCredentials());
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper
            ->exchangeAuthorizationCodeForToken($code, $companyId);
        session(['sessionAccessToken' => $accessToken]);
    }

    public function saveAccessToken($userId)
    {
        $accessToken = session('sessionAccessToken');
        $this->fill([
            'user_id' => $userId,
            'refresh_token' => $accessToken->getRefreshToken(),
            'refresh_token_expires_at' => $accessToken->getRefreshTokenExpiresAt(),
            'refresh_token_validation_period' => $accessToken->getRefreshTokenValidationPeriodInSeconds(),
            'company_id' => $accessToken->getRealmID()
        ]);
        $this->save();
    }

    public function checkIfAccessTokenHasExpired($accessToken)
    {

    }

    public function getCredentials()
    {
        return [
            'auth_mode' => $this->auth_mode,
            'ClientID' => $this->client_id,
            'ClientSecret' => $this->client_secret,
            'RedirectURI' => $this->redirect_uri,
            'scope' => $this->scope,
            'baseUrl' => env('AUTHORIZATION_REQUEST_URL'),
            'state' => $this->state
        ];
    }

    public function getDataServiceObject()
    {
        return DataService::Configure($this->getAuthCredentials());
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->auth_mode = env('OAUTH_MODE');
        $this->client_id = env('CLIENT_ID');
        $this->client_secret = env('CLIENT_SECRET');
        $this->redirect_uri = env('OAUTH_REDIRECT_URI');
        $this->scope = env('OAUTH_SCOPE');
        $this->base_url = env('AUTHORIZATION_REQUEST_URL');
    }

    public function generateCsrf()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
//                .chr(125);// ""
            return $uuid;
        }
    }

    public function checkIfGuidIsValid($guid)
    {
        if ($this->checkGuidIsInDb($guid) &&
            $this->checkGuidIsLessThan5MinutesOld($guid)) {
            $this->consumeToken($guid);
            return true;
        } else {
            return false;
        }
    }

    public function checkGuidIsInDb($guid)
    {
        $statement = "SELECT guid from quickbook_csrf_tokens where guid = '" . $guid . "'";
        $response = DB::select($statement);
        if (!empty($response[0]->guid)) {
            return true;
        } else {
            return false;
        }
    }

    public function consumeToken($guid)
    {
        $statement = "Update quickbook_csrf_tokens set consumed = true, 
                          updated_at = NOW() where guid = '" . $guid . "'";
        DB::select($statement);
    }

    public function checkGuidIsLessThan5MinutesOld($guid)
    {
        $statement = "SELECT created_at from quickbook_csrf_tokens where guid = '" . $guid . "'";
        $date = DB::select($statement);
        $created = Carbon::createFromTimeString($date[0]->created_at);
        $now = Carbon::now();
        if ($now->diffInMinutes($created) < 5) {
            return true;
        } else {
            return false;
        }
    }

    public function addGuidToTable($guid)
    {
        $now = Carbon::now('gmt');
        $statement = "Insert Into quickbook_csrf_tokens (guid, consumed, expired, created_at, updated_at)" .
            " Values ('" . $guid . "', false, false, NOW(), NOW())";
//        dd($statement);
        DB::insert($statement);
    }

    public function setState($state)
    {
        $this->state = json_encode($state);
    }

    public function getCredsWithState()
    {
        return [
            'auth_mode' => $this->auth_mode,
            'ClientID' => $this->client_id,
            'ClientSecret' => $this->client_secret,
            'RedirectURI' => $this->redirect_uri,
            'scope' => $this->scope,
            'baseUrl' => $this->base_url,
            'state' => $this->state
        ];
    }

    public function getAuthCredentials()
    {
        return $this->credentials = [
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'RedirectURI' => env('OAUTH_REDIRECT_URI'),
            'scope' => env('OAUTH_SCOPE'),
            'baseUrl' => env('AUTHORIZATION_URL_REQUEST_URL')
        ];
    }

    public function getCompanyInfo()
    {

        $dataService = DataService::Configure($this->credentials);
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        echo $authUrl;
    }

    public function getTheCompanyInfo()
    {

        $dataService = DataService::Configure($this->getCredentials());
        $accessToken = session('sessionAccessToken');
        $dataService->updateOAuth2Token($accessToken);

        // get company information and return it
        $companyInfo = $dataService->getCompanyInfo();
        session(['companyInfo' => $companyInfo]);
    }

    public function checkIfCustomerExists(\App\Customer $customer)
    {

    }

    public function addCustomer(\App\User $customer)
    {
//        if (!$this->checkIfCustomerExists($customer)){
//            $dataService = DataService::Configure($this->getCredentials());
//            $accessToken = session('sessionAccessToken');
//            $accessToken = unserialize(base64_decode($accessToken));
//            $dataService->updateOAuth2Token($accessToken);
//            $companyInfo = $dataService->FindAll('customer');
//
//        }

        $dataService = DataService::Configure($this->getCredentials());
        $accessToken = session('sessionAccessToken');
        $accessToken = unserialize(base64_decode($accessToken));
        $dataService->updateOAuth2Token($accessToken);
        $cust = $this->createQBCustomerObject($customer);
        return $dataService->Add($cust);

    }

    public function createQBCustomerObject(\App\User $customer)
    {
        return QuickBooksOnline\API\Facades\Customer::create([
            "GivenName" => $customer->name,
            "PrimaryPhone" => [
                "FreeFormNumber" => $customer->phone
            ]
        ]);
    }

    public function getGuidFromRequest($request)
    {
        return collect(json_decode($request))->toArray()['guid'];
    }
}
