<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubStatus extends Model
{
    //
    protected $table = 'sub_status';
    protected $guarded = [];


    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }

    public function initiated($contractor_id, $job_task_id)
    {
        $this->user_id = $contractor_id;
        $this->job_task_id = $job_task_id;
        $this->status = 'sub.initiated';
        $this->status_number = 1;

        return $this->saveStatus($this);
    }

    public function saveStatus($status)
    {
        try {
            $status->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

}
