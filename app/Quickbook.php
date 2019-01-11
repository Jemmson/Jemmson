<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quickbook extends Model
{
    //
    protected $fillable = [
        'company_id',
        'user_id',
    ];
}
