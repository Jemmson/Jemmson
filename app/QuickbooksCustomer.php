<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickbooksCustomer extends Model
{
    //
    protected $table = 'quickbooks_customer';

    public static function getAssociatedCustomers($name, $contractorId)
    {

        $quickbookCustomers = QuickbooksCustomer::where('given_name', 'like', '%' . $name . '%')
            ->where('contractor_id', '=', $contractorId)
            ->get();


        if (empty($quickbookCustomers->first())) {
            $quickbookCustomers = QuickbooksCustomer::where('family_name', 'like', '%' . $name . '%')
                ->where('contractor_id', '=', $contractorId)
                ->get();
        }

        return $quickbookCustomers;

    }
}
