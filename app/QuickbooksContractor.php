<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickbooksContractor extends Model
{
    //
    protected $table = 'quickbooks_contractor';

    public static function ContractorExists(
        $email,
        $phone,
        $givenName,
        $familyName,
        $companyName
    )
    {
        $contractor = QuickbooksContractor::where('primary_email_addr', '=', $email)->
        where('primary_phone', '=', $phone)->
        where('given_name', '=', $givenName)->
        where('company_name', '=', $companyName)->
        where('family_name', '=', $familyName)->get()->first();

        if (is_null($contractor)) {
            return false;
        } else {
            return true;
        }

    }
}
