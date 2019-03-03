<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractorCustomer extends Model
{
    // This table is used to keep track o which customers are associated to which contractors
    // A contractor should not have knowledge of other contractors customers

    protected $table = 'contractor_customer';

    public function checkIfCustomerCurrentlyExistsForContractor($contractorId, $customerId)
    {
        return empty(ContractorCustomer::select()
            ->where('contractor_id', '=', $contractorId)
            ->where('customer_id', '=', $customerId)->get()->first());
    }

    public function associateCustomer($contractorId, $customerId)
    {
        $this->contractor_id = $contractorId;
        $this->customer_id = $customerId;
        $this->save();
    }
}
