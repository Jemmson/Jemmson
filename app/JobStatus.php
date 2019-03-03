<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    protected $table = 'job_status';
    protected $fillable = [
        'job_id',
        'status_number',
        'status'
    ];

    public function setStatus($jobId, $status)
    {
        $statusNumber = $this->getStatusNumer($status);
        
        $this->fill([
           'job_id' => $jobId,
           'status_number' => $statusNumber,
           'status' => $status
        ]);
        $this->save();
    }

    public function getStatusNumer($status)
    {
        switch ($status){
            case 'initiated':
                return 1;
                break;
            case 'in_progress':
                return 2;
                break;
            case 'completed':
                return 3;
                break;
            case 'approved':
                return 4;
                break;
            case 'sent':
                return 5;
                break;
            case 'declined':
                return 6;
                break;
            case 'paid':
                return 7;
                break;
        }
    }
}
