<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickbooksContractor extends Model
{
    //
    protected $table = 'quickbooks_contractor';

    public static function ContractorExists($request)
    {

        $contractor = QuickbooksContractor::where('primary_email_addr', '=', $request->email)->
        where('primary_phone', '=', $request->phone)->
        where('given_name', '=', $request->givenName)->
        where('company_name', '=', $request->companyName)->
        where('family_name', '=', $request->familyName)->get()->first();

        if (is_null($contractor)) {
            return false;
        } else {
            return true;
        }

    }
}
