<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    public static function getAllTasksForGeneralByTaskIds($generalId, Array $jobTask_taskids)
    {
        return ContractorContractor::where('contractor_id', '=', $generalId)
            ->whereIn('task_id', $jobTask_taskids)
            ->get();
    }

    public static function getSubsNameAndId($authUser)
    {
        $associatedSubs = ContractorContractor::select(['subcontractor_id'])
            ->where('contractor_id', '=', $authUser)
            ->get();

        $ids = [];

        foreach ($associatedSubs as $associatedSub) {
            array_push($ids, $associatedSub->subcontractor_id);
        }

        $subs = User::select(['id', 'name'])->whereIn('id', $ids)->get();

        foreach ($subs as $sub) {
            $sub['contractor'] = Contractor::select(['company_name'])
                ->where('user_id', '=', $sub->id)
                ->get()->first();
        }


        return $subs;

    }

}
