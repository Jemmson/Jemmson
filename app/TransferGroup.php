<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferGroup extends Model
{
    //
    protected $table = 'transfer_group';
    protected $guarded = [];

    public function create($attributes)
    {
        $this->general_id = $attributes['general_id'];
        $this->general_amount = $attributes['general_amount'];
        $this->sub_amount = $attributes['sub_amount'];
        $this->jemmson_amount = $attributes['jemmson_amount'];
        $this->stripe_amount = $attributes['stripe_amount'];
        $this->job_id = $attributes['job_id'];
        $this->job_task_id = $attributes['job_task_id'];
        $this->sub_id = $attributes['sub_id'];
        $this->transfer_group_guid = $attributes['transfer_group_guid'];

        try {
            $this->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function getGuid()
    {
        return $this->transfer_group_guid;
    }

}
