<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorContractor extends Model
{
    //
    use SoftDeletes;
    protected $table = 'contractor_contractor';
}
