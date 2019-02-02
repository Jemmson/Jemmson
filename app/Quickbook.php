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
        foreach($nd as $k => $c){
            $creds->put($k, $c);
        }
        $this->credentials = $creds->toArray();
    }

    public function getCredentials()
    {
        return [
            'auth_mode' => $this->auth_mode,
            'ClientID' => $this->client_id,
            'ClientSecret' => $this->client_secret,
            'RedirectURI' =>  $this->redirect_uri,
            'scope' =>  $this->scope,
            'baseUrl' => env('AUTHORIZATION_REQUEST_URL'),
            'state' => $this->state
        ];
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
            'RedirectURI' =>  $this->redirect_uri,
            'scope' =>  $this->scope,
            'baseUrl' =>  $this->base_url,
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
}
