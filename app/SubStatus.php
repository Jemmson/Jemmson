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

    public function setStatus($user_id, $job_task_id, $status)
    {

        $ss = SubStatus::where('user_id', '=', $user_id)
            ->where('job_task_id', '=', $job_task_id)
            ->orderBy('created_at', 'desc')->select('status')
            ->get()->first();

        if (empty($this->checkStatus($user_id, $job_task_id, $status)) || $ss['status'] != $status ) {
            $statusNumber = $this->getStatusNumber($status);
            $this->fill([
                'user_id' => $user_id,
                'status_number' => $statusNumber,
                'job_task_id' => $job_task_id,
                'status' => $status
            ]);
            $this->save();
        }
    }

    private function checkStatus($user_id, $job_task_id, $status)
    {
        return SubStatus::where("status", "=", $status)
            ->where("user_id", "=", $user_id)
            ->where("job_task_id", "=", $job_task_id)->get()->first();
    }



    public function getStatusNumber($status)
    {
        switch ($status) {
            case 'initiated':
                return 1;
                break;
            case 'sent_a_bid':
                return 2;
                break;
            case 'accepted':
                return 3;
                break;
            case 'denied':
                return 4;
                break;
            case 'waiting_for_customer_approval':
                return 5;
                break;
            case 'customer_changes_bid':
                return 6;
                break;
            case 'canceled_by_customer':
                return 7;
                break;
            case 'canceled_by_general':
                return 8;
                break;
            case 'canceled_bid_task':
                return 9;
                break;
            case 'approved_by_customer':
                return 10;
                break;
            case 'finished_job':
                return 11;
                break;
            case 'finished_job_denied_by_contractor':
                return 12;
                break;
            case 'customer_changes_finished_task':
                return 13;
                break;
            case 'finished_job_approved_by_contractor':
                return 14;
                break;
            case 'waiting_for_customer_payment':
                return 15;
                break;
            case 'paid':
                return 16;
                break;
        }
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
