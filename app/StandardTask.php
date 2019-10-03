<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardTask extends Model
{
    //
    use SoftDeletes;
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
