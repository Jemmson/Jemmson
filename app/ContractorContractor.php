<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorContractor extends Model
{
    //
    use SoftDeletes;
    protected $table = 'contractor_contractor';

    public function create($generalId, $subId)
    {
        $this->contractor_id = $generalId;
        $this->subcontractor_id = $subId;

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
