<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickBooksAccessToken extends Model
{
    //
    protected $table = 'quick_books_access_token';
    protected $fillable = [
        'user_id',
        'access_token_key',
        'token_type',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'access_token_validation_period',
        'refresh_token_validation_period',
        'client_id',
        'client_secret',
        'realm_id',
        'base_url',
    ];
}
