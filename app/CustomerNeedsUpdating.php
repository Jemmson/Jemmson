<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerNeedsUpdating extends Model
{
    //
    protected $table = 'customer_needs_updating';


    public static function addEntryToCustomerNeedsUpdatingIfNeeded(
        $contractorId,
        $customerId,
        $quickbookId
    )
    {
        $customer = CustomerNeedsUpdating::where('contractor_id','=', $contractorId)
            ->where('customer_id', '=',$customerId)->get()->first();

        if(empty($customer)){
            $cnu = new CustomerNeedsUpdating();
            $cnu->contractor_id = $contractorId;
            $cnu->customer_id = $customerId;
            $cnu->quickbooks_id = $quickbookId;

            try {
                $cnu->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }
        }
    }

    public function customerNeedsUpdating($customerId)
    {
        $customerEntries = CustomerNeedsUpdating::where('customer_id', '=',$customerId)->get();

        foreach ($customerEntries as $customer){
            $customer->needs_updating = true;
            $customer->save();
        }
    }
}
