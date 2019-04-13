<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Exception\SdkException;
use App\QuickbooksContractor;
use App\QuickbooksCustomer;
use App\QuickBookCSRFToken;
use App\User;
use App\Location;


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;

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

    public function checkIfRefreshTokenIsValid($userId)
    {
        $qbCreds = $this->getQuickBookCredentialsFromDB($userId);
        return $qbCreds->refresh_token_expires_at - time() > 0;

    }

    public function getQuickBookCredentialsFromDB($userId)
    {
        return Quickbook::select()->where('user_id', '=', $userId)->get()->first();
    }

    public function checkIfSessionAccessTokenExists()
    {
        return !empty(session('sessionAccessToken'));
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
        $refreshTokenExpiresAt = $this->getAccessTokenTimeStamp($accessToken->getRefreshTokenExpiresAt());

        // does a company id exist or that user? If yes then update it, if no then add an entry
        $qbUser = Quickbook::select()->where('user_id', '=', $userId)->get()->first();

        if (!empty($qbUser)) {
            if (!empty($qbUser->company_id)) {
                $qbUser->refresh_token = $accessToken->getRefreshToken();
                $qbUser->refresh_token_validation_period = $accessToken->getRefreshTokenValidationPeriodInSeconds();
                $qbUser->save();
            } else {
                if (!empty($accessToken->getRealmID())) {
                    $qbUser->company_id = $accessToken->getRealmID();
                    $qbUser->refresh_token = $accessToken->getRefreshToken();
                    $qbUser->refresh_token_validation_period = $accessToken->getRefreshTokenValidationPeriodInSeconds();
                    $qbUser->save();
                } else {
                    // TODO: throw an exception that the refresh token can not be saved
                }
            }
        } else {
            if (!empty($accessToken->getRealmID())) {
                $this->fill([
                    'user_id' => $userId,
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'refresh_token_expires_at' => $refreshTokenExpiresAt,
                    'refresh_token_validation_period' => $accessToken->getRefreshTokenValidationPeriodInSeconds(),
                    'company_id' => $accessToken->getRealmID()
                ]);
                $this->save();
            } else {
                // TODO: throw an exception that the refresh token can not be saved
            }
        }
    }

    public function checkOrUpdateAccessToken()
    {
        $accessToken = session('sessionAccessToken');
        $accessTokenTimestamp = $this->getAccessTokenTimeStamp($accessToken->getAccessTokenExpiresAt());
        if (!$this->checkIfAccessTokenHasNotExpired($accessTokenTimestamp)) {
            // has expired
            $this->refreshAccessToken();
        }
    }

    public function refreshAccessToken()
    {
//        $oauth2LoginHelper = new OAuth2LoginHelper($ClientID, $ClientSecret);
//        $accessTokenObj = $oauth2LoginHelper->
//        refreshAccessTokenWithRefreshToken($theRefreshTokenValue);
//        $accessTokenValue = $accessTokenObj->getAccessToken();
//        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        $user_id = Auth::user()->id;

        // get new Token
        $oauth2LoginHelper = '';
        try {
            $oauth2LoginHelper = new OAuth2LoginHelper(env('CLIENT_ID'), env('CLIENT_SECRET'), env('OAUTH_REDIRECT_URI'));
        } catch (SdkException $exception) {
            Log::debug($exception);
        }

        $refreshToken = $this->getQuickBookCredentialsFromDB(Auth::user()->getAuthIdentifier());
        $accessToken = $oauth2LoginHelper->
        refreshAccessTokenWithRefreshToken($refreshToken->refresh_token);
//        $refreshTokenValue = $accessToken->getRefreshToken();

        // save the refresh token
        session(['sessionAccessToken' => $accessToken]);
        $this->saveAccessToken($user_id);
//        $this->saveNewRefreshToken($refreshTokenValue, $user_id);
    }

    public function getAccessTokenTimeStamp($date)
    {
        $d = Carbon::createFromFormat('Y/m/d H:i:s', $date);
        return $d->timestamp;
    }

    public function checkIfAccessTokenHasNotExpired($accessTokenDate)
    {
        return $accessTokenDate - time() > 0;
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
        if (
            $this->checkGuidIsInDb($guid) &&
            $this->checkGuidIsLessThan5MinutesOld($guid)) {
            $this->tokenHasNotBeenConsumed($guid);
            return true;
        } else {
            return false;
        }
    }

    public function checkGuidIsInDb($guid)
    {

        $guid = QuickBookCSRFToken::select('guid')
            ->where("guid", "=", $guid)
            ->get()->first()->guid;

        if (!empty($guid)) {
            return true;
        } else {
            return false;
        }
    }

    public function tokenHasNotBeenConsumed($guid)
    {

        $qbcsrf = QuickBookCSRFToken::select()->where("guid", "=", $guid)->get()->first();
        if ($qbcsrf->consumed) {
            return false;
        } else {
            $qbcsrf->consumed = true;
            $qbcsrf->save();
            return true;
        }

    }

    public function checkGuidIsLessThan5MinutesOld($guid)
    {
//        $statement = "SELECT created_at from quickbook_csrf_tokens where guid = '" . $guid . "'";
//        $date = DB::select($statement);

        $created = QuickBookCSRFToken::select('created_at')->where("guid", "=", $guid)->get()->first()->created_at;
        $now = Carbon::now();
        $difference = $now->diffInMinutes($created);
        if ($difference < 5) {
            return true;
        } else {
            return false;
        }
    }

    public function addGuidToTable($guid)
    {
        $qbcsrf = new QuickBookCSRFToken();
        $qbcsrf->guid = $guid;
        $qbcsrf->consumed = false;
        $qbcsrf->expired = false;
        $qbcsrf->save();
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

//        $dataService = DataService::Configure($this->getCredentials());
//        $accessToken = $this->checkOrUpdateAccessToken();
//        $dataService->updateOAuth2Token($accessToken);
//        $cust = $this->createQBCustomerObject($customer);
//        return $dataService->Add($cust);

        $accessToken = session('sessionAccessToken');
        $qbUser = Quickbook::select()->where('user_id', '=', Auth::user()->getAuthIdentifier())->get()->first();

        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'accessTokenKey' => $accessToken->getAccessToken(),
            'refreshTokenKey' => $qbUser->refresh_token,
            'QBORealmID' => $qbUser->company_id,
            'baseUrl' => "development"
        ));
//        $dataService->setLogLocation("/Users/hlu2/Desktop/newFolderForLog");
// Add a customer
        $customerObj = Customer::create([
//            "BillAddr" => [
//                "Line1"=>  "123 Main Street",
//                "City"=>  "Mountain View",
//                "Country"=>  "USA",
//                "CountrySubDivisionCode"=>  "CA",
//                "PostalCode"=>  "94042"
//            ],
//            "Notes" =>  "Here are other details.",
//            "Title"=>  "Mr",
//            "GivenName"=>  $customer->name,
//            "MiddleName"=>  "1B",
//            "FamilyName"=>  "King",
//            "Suffix"=>  "Jr",
            "FullyQualifiedName" => $customer->name,
//            "CompanyName"=>  "King Evial",
            "DisplayName" => $customer->name,
            "PrimaryPhone" => [
                "FreeFormNumber" => $customer->phone
            ],
//            "PrimaryEmailAddr"=>  [
//                "Address" => "evilkingw@myemail.com"
//            ]
        ]);
        $resultingCustomerObj = $dataService->Add($customerObj);
        $error = $dataService->getLastError();
        if ($error) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
        } else {
            var_dump($resultingCustomerObj);
        }

    }


    public function checkIfQuickbooksCustomerExists(\App\User $customer)
    {
        $accessToken = session('sessionAccessToken');
        $qbUser = Quickbook::select()->where('user_id', '=', Auth::user()->getAuthIdentifier())->get()->first();
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'accessTokenKey' => $accessToken->getAccessToken(),
            'refreshTokenKey' => $qbUser->refresh_token,
            'QBORealmID' => $qbUser->company_id,
            'baseUrl' => "development"
        ));
        $entities = $dataService->Query("Select count(*) from Invoice");
        $error = $dataService->getLastError();
        if ($error) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
            exit();
        }
// Echo some formatted output
        var_dump($entities);
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


    public function updateAccessToken()
    {
        if ($this->checkIfSessionAccessTokenExists()) {
            $accessToken = session('sessionAccessToken');
            $accessTokenTimestamp = $this->getAccessTokenTimeStamp($accessToken->getAccessTokenExpiresAt());
            if ($this->checkIfAccessTokenHasNotExpired($accessTokenTimestamp)) {
                return true;
            } else {
                if ($this->checkIfRefreshTokenIsValid(Auth::user()->getAuthIdentifier())) {
                    $this->refreshAccessToken();
                    return true;
                } else {
//                      need to take them through the authorization flow again
//                        if they are still using quickbooks
                    return false;
                }
            }
        } else {
            if ($this->checkIfRefreshTokenIsValid(Auth::user()->getAuthIdentifier())) {
                $this->refreshAccessToken();
                return true;
            } else {
//                      need to take them through the authorization flow again
//                        if they are still using quickbooks
                return false;
            }
        }
    }

    public function isContractorThatUsesQuickbooks()
    {
        if (Auth::user()->contractor != null) {
            return Auth::user()->usertype === 'contractor' &&
            Auth::user()->contractor->accounting_software == 'quickBooks';
        }

        return false;
    }

    public function contractorSubscriptionIsStillActive()
    {
        // TODO: figure out how to check that the quickbooks account is still active for the user
        return true;
    }

    public function syncCustomerInformationFromQB()
    {
        $allQBCustomers = $this->pullAllQBCustomersFromAccount();

        foreach ($allQBCustomers as $customer) {
            if (
                !$this->checkIfCustomerInQuickbooksCustomerTable($customer) &&
                !$this->checkIfCustomerInQuickbooksContractorTable($customer)
            ) {
                if (empty($customer->CompanyName)) {
                    $this->addCustomerToCustomerTable($customer);
                } else {
                    $this->addCustomerToContractorTable($customer);
                }
            }
        }

//        $allJemmsonCustomers = $this->pullAllJemmsonCustomersAssociatedToContractor();
//        $allJemmsonContractors = $this->pullAllJemmsonSubContractorsAssociatedToContractor();
//        $allCustomerUserInfo = $this->getAllUserInformation($allJemmsonCustomers);
//        $allContractorUserInfo = $this->getAllUserInformation($allJemmsonContractors);
//        $allCustomerLocationInfo = $this->getAllCustomerLocationInfo($allJemmsonCustomers);
//        $this->syncCustomerData($allQBCustomers, $allJemmsonCustomers, $allCustomerUserInfo, $allCustomerLocationInfo);
    }

    public function returnNonNullAttribute($attribute)
    {
        if (empty($attribute)) {
            return 'NULL';
        } else {
            return $attribute;
        }
    }

    public function digitIsANumber($digit)
    {
        if(
            $digit == '0' ||
            $digit == '1' ||
            $digit == '2' ||
            $digit == '3' ||
            $digit == '4' ||
            $digit == '5' ||
            $digit == '6' ||
            $digit == '7' ||
            $digit == '8' ||
            $digit == '9'
        ){
            return true;
        } else {
            return false;
        }
    }

    public function formatPhoneNumber($phone)
    {
//        should only be digits
        $digitsOnly = '';
        for ($i = 0; $i < strlen($phone); $i++) {
            if($this->digitIsANumber($phone[$i])){
                $digitsOnly = $digitsOnly . $phone[$i];
            }
        }
        return $digitsOnly;
    }


    public function addCustomerToCustomerTable($customer)
    {
        $cust = new QuickbooksCustomer();
        $cust->quickbooks_id = $customer->Id;
        $cust->contractor_id = Auth::user()->getAuthIdentifier();
        $cust->customer_id = $customer->Id;
        $cust->given_name = $this->returnNonNullAttribute($customer->GivenName);
        $cust->middle_name = $this->returnNonNullAttribute($customer->MiddleName);
        $cust->family_name = $this->returnNonNullAttribute($customer->FamilyName);
        $cust->fully_qualified_name = $this->returnNonNullAttribute($customer->FullyQualifiedName);
        if (!is_null($customer->PrimaryPhone)) {
            $cust->primary_phone = $this->formatPhoneNumber($customer->PrimaryPhone->FreeFormNumber);
        }
        $cust->primary_email_addr = $this->returnNonNullAttribute($customer->PrimaryEmailAddr);
        try {
            $cust->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 200);
        }
    }

    public function addCustomerToContractorTable($customer)
    {
        $cust = new QuickbooksContractor();
        $cust->quickbooks_id = $customer->Id;
        $cust->contractor_id = Auth::user()->getAuthIdentifier();
        $cust->sub_contractor_id = $customer->Id;
        $cust->company_name = $this->returnNonNullAttribute($customer->CompanyName);
        $cust->given_name = $this->returnNonNullAttribute($customer->GivenName);
        $cust->middle_name = $this->returnNonNullAttribute($customer->MiddleName);
        $cust->family_name = $this->returnNonNullAttribute($customer->FamilyName);
        $cust->fully_qualified_name = $this->returnNonNullAttribute($customer->FullyQualifiedName);
        if (!is_null($customer->PrimaryPhone)) {
            $cust->primary_phone = $this->formatPhoneNumber($customer->PrimaryPhone->FreeFormNumber);
        }
        $cust->primary_email_addr = $this->returnNonNullAttribute($customer->PrimaryEmailAddr);

        try {
            $cust->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 200);
        }
    }


    public function checkIfCustomerInQuickbooksCustomerTable($customer)
    {
        $cust = QuickbooksCustomer::select()->where('customer_id', '=', $customer->Id)
            ->get()->first();
        if (empty($cust)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkIfCustomerInQuickbooksContractorTable($customer)
    {
        $cust = QuickbooksContractor::select()->where('sub_contractor_id', '=', $customer->Id)
            ->get()->first();
        if (empty($cust)) {
            return false;
        } else {
            return true;
        }
    }


    public function syncCustomerData($allQBCustomers, $allJemmsonCustomers, $allCustomerUserInfo, $allCustomerLocationInfo)
    {
        $customerQuickBooks_idThatAreNull = [];
        foreach ($allJemmsonCustomers as $jemmsonCustomer) {
            foreach ($allQBCustomers as $qbCustomer) {
                if (empty($jemmsonCustomer->quickbooks_id)) {
                    array_push($customerQuickBooks_idThatAreNull, $jemmsonCustomer);
                } else {
                    if ($jemmsonCustomer->quickbooks_id == $qbCustomer->quickooks_id) {
                        $this->updateCustomer($jemmsonCustomer, $qbCustomer);
                    }
                }
            }
        }
    }

    public function updateCustomer($jemmsonCustomer, $qbCustomer)
    {

    }

    public function getAllCustomerLocationInfo($customers)
    {
        $location_id_Array = [];
        foreach ($customers as $customer) {
            array_push($location_id_Array, $customer->location_id);
        }
        return Location::find($location_id_Array);
    }

    public function getAllUserInformation($users)
    {
        $user_id_Array = [];
        foreach ($users as $user) {
            array_push($user_id_Array, $user->user_id);
        }
        return User::find($user_id_Array);
    }

    public function pullAllJemmsonSubContractorsAssociatedToContractor()
    {
        $contractorIdsArray = [];
        $contractorIds = ContractorContractor::where('contractor_id', '=',
            User::find(Auth::user()->getAuthIdentifier())->contractor()->get()->first()->id);
        foreach ($contractorIds as $contractorId) {
            array_push($contractorIdsArray, $contractorId->subcontractor_id);
        }
        return Contractor::find($contractorIdsArray);
    }

    public function pullAllJemmsonCustomersAssociatedToContractor()
    {
        return Contractor::find()
            ->customers()
            ->get(['user_id', 'location_id']);
    }

    public function pullAllQBCustomersFromAccount()
    {
        $accessToken = session('sessionAccessToken');
        $qbUser = Quickbook::select()->where('user_id', '=', Auth::user()->getAuthIdentifier())->get()->first();
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'accessTokenKey' => $accessToken->getAccessToken(),
            'refreshTokenKey' => $qbUser->refresh_token,
            'QBORealmID' => $qbUser->company_id,
            'baseUrl' => "development"
        ));
        $entities = $dataService->Query(
            "SELECT 
                      Id, 
                      GivenName, 
                      MiddleName,
                      FamilyName,
                      FullyQualifiedName,
                      CompanyName,
                      DisplayName,
                      PrimaryPhone
                    FROM Customer"
        );
        return $entities;
    }

    public function getLatestCustomerDataFromQB($qbId)
    {
        $accessToken = session('sessionAccessToken');
        $qbUser = Quickbook::select()->where('user_id', '=', Auth::user()->getAuthIdentifier())->get()->first();
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'accessTokenKey' => $accessToken->getAccessToken(),
            'refreshTokenKey' => $qbUser->refresh_token,
            'QBORealmID' => $qbUser->company_id,
            'baseUrl' => "development"
        ));

        $entities = $dataService->Query("SELECT * FROM Customer WHERE 'Id' = $qbId");

        return $entities;
    }

}
