<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BidContractorJobTask extends Model
{
    protected $table = "bid_contractor_job_task";
    protected $fillable = [
        'contractor_id',
        'job_id',
        'task_id',
        'bid_price',
        'accepted',
    ];

    /**
     * Get the task this bid belongs to
     *
     * @return Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

}
