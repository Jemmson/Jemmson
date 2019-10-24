<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    //
    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
