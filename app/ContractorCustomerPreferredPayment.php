<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorCustomerPreferredPayment extends Model
{
    //
    protected $table = 'contractor_customer_preferred_payment';
    protected $guarded = [];
    use SoftDeletes;
}
