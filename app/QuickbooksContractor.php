<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class QuickbooksContractor extends Model
{
    //
    protected $table = 'quickbooks_contractor';

    public static function ContractorExists($request)
    {

        $contractor = QuickbooksContractor::
        where('company_name', '=', $request->companyName)->
        where('quickbooks_id', '=', $request->quickbooksId)->get()->first();

        if (is_null($contractor)) {
            return false;
        } else {

            return $contractor;
        }

    }

    public static function addContractorToQuickbooksContractorTable($request, $resultingCustomerObj)
    {
        $qbc = new QuickbooksContractor();

        $qbc->primary_email_addr = $request->email;
        $qbc->primary_phone = $request->phone;
        $qbc->contractor_id = Auth::user()->getAuthIdentifier();
        $qbc->given_name = $request->firstName;
        $qbc->family_name = $request->lastName;
        $qbc->sub_contractor_id = $resultingCustomerObj->Id;
        $qbc->quickbooks_id = $resultingCustomerObj->Id;
        $qbc->company_name = $request->companyName;
        
        try {
            $qbc->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }
}
