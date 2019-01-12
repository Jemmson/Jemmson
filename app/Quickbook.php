<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp;
use QuickBooksOnline\API\DataService\DataService;

class Quickbook extends Model
{
    //
    protected $fillable = [
        'access_token',
        'code',
        'company_id',
        'realmId',
        'state',
        'user_id',
    ];

    protected $credentials = [];

    public function addCredentials($newCredentials)
    {
        $nd = collect($newCredentials);
        $creds = collect($this->credentials);
        foreach($nd as $k => $c){
            $creds->put($k, $c);
        }

        $this->credentials = $creds->toArray();
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->credentials = [
            'auth_mode' => 'oauth2',
            'ClientID' => env('CLIENT_ID'),
            'ClientSecret' => env('CLIENT_SECRET'),
            'RedirectURI' => env('OAUTH_REDIRECT_URI'),
            'scope' => env('OAUTH_SCOPE'),
            'baseUrl' => env('AUTHORIZATION_REQUEST_URL')
        ];

//        $dataService = DataService::Configure(array(
//
//        ));

//        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
//        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
//
//        //set the access token using the auth object
////                if (isset($_SESSION['sessionAccessToken'])) {
//
////                    $accessToken = $_SESSION['sessionAccessToken'];
//        $accessTokenJson = array('token_type' => 'bearer',
//            'access_token' => $accessToken->getAccessToken(),
//            'refresh_token' => $accessToken->getRefreshToken(),
//            'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
//            'expires_in' => $accessToken->getAccessTokenExpiresAt()
//        );
//        $dataService->updateOAuth2Token($accessToken);
//        $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
//        $CompanyInfo = $dataService->getCompanyInfo();
////                }

    }

    public function getCompanyInfo()
    {

        $dataService = DataService::Configure($this->credentials);

//        dd($dataService);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

//        echo gettype($authUrl);

        echo $authUrl;

//        return ['auth_url' => $authUrl];

//        dd($authUrl);

//        $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
//        $accessToken = $dataService->updateOAuth2Token($refreshedAccessTokenObj);


//        dd($accessToken);
//
//        $accessTokenJson = array('token_type' => 'bearer',
//            'access_token' => $accessToken->getAccessToken(),
//            'refresh_token' => $accessToken->getRefreshToken(),
//            'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
//            'expires_in' => $accessToken->getAccessTokenExpiresAt()
//        );
//        $dataService->updateOAuth2Token($accessToken);
//        $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
//        $CompanyInfo = $dataService->getCompanyInfo();
//    }


//        $client = new GuzzleHttp\Client();
//        $res = $client->request('GET', 'https://api.github.com/user',
//            $this->credentials
//        );
//        echo $res->getStatusCode(); // "200"
//        echo $res->getHeader('content-type'); // 'application/json; charset=utf8'
//        echo $res->getBody(); // {"type":"User"...'
//
//        // Send an asynchronous request.
//        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
//        $promise = $client->sendAsync($request)->then(function ($response) {
//            echo 'I completed! ' . $response->getBody();
//        });
//        $promise->wait();
//    }
    }
}
