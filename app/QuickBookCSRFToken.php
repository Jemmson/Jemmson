<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickBookCSRFToken extends Model
{
    //
    protected $table = 'quickbook_csrf_tokens';
    protected $guarded = [];
}
