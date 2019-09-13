<?php

namespace App;

use App\Http\Controllers\CustomerController;
use Illuminate\Database\Eloquent\Model;

class ContractorCustomer extends Model
{
    // This table is used to keep track o which customers are associated to which contractors
    // A contractor should not have knowledge of other contractors customers

    protected $table = 'contractor_customer';

    public function checkIfCustomerCurrentlyExistsForContractor($contractorId, $customerId)
    {
        return empty(ContractorCustomer::select()
            ->where('contractor_user_id', '=', $contractorId)
            ->where('customer_user_id', '=', $customerId)->get()->first());
    }

    public static function isCustomerAssociatedWithContractor($contractorId, $customerId)
    {
        return 0 < count(ContractorCustomer::where('contractor_user_id', '=', $contractorId)
            ->where('customer_user_id', '=', $customerId)->get());
    }

    public function associateCustomer($contractorId, $customerId)
    {
        if ($this->checkIfCustomerCurrentlyExistsForContractor($contractorId, $customerId)) {
            $this->contractor_user_id = $contractorId;
            $this->customer_user_id = $customerId;

            try {
                $this->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }

        }
    }

    public static function addQuickBookIdToAssociation($contractorId, $customerId, $quickbookId)
    {
        $cc = ContractorCustomer::where('contractor_user_id', '=', $contractorId)
            ->where('customer_user_id', '=', $customerId)->get()->first();
        $cc->quickbooks_id = $quickbookId;

        try {
            $cc->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

//        ContractorCustomer::where('contractor_user_id', '=', 1)->where('customer_user_id', '=', 2)
    }

    public static function getCustomerIdsAsAnArray($users)
    {
        $ids = [];
        foreach ($users as $user){
            array_push($ids, $user->id);
        }
        return $ids;
    }

    public static function filterCustomerUserIds($users)
    {
        $ids = [];
        foreach ($users as $user){
            array_push($ids, [
                'user_id' => $user->customer_user_id,
                'quickbooks_id' => $user->quickbooks_id
            ]);
        }
        return $ids;
    }

    public static function getAssociatedCustomers($users, $contractorId)
    {
        $customerIds = ContractorCustomer::getCustomerIdsAsAnArray($users);
        $filteredCustomers = ContractorCustomer::select('customer_user_id', 'quickbooks_id')
            ->whereIn('customer_user_id', $customerIds)
            ->where('contractor_user_id', '=', $contractorId)->get();
        return ContractorCustomer::filterCustomerUserIds($filteredCustomers);
    }
}
