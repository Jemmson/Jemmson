<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    //
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function elements()
    {
        return $this->hasMany(Element::class);
    }
}
