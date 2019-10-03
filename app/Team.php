<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Spark\Team as SparkTeam;

class Team extends SparkTeam
{
    //
    use SoftDeletes;
}
