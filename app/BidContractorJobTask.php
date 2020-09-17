<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BidContractorJobTask extends Model
{
    use SoftDeletes;
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
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the job_task this bid belongs to
     *
     * @return Task
     */
    public function jobTask()
    {
        return $this->belongsTo(JobTask::class, 'job_task_id', 'id');
    }
    
    public function contractor()
    {
        return $this->belongsTo(User::class);
    }

    public function contractorSubContractorPreferredPayment()
    {
        return $this->hasOne(ContractorSubcontractorPreferredPayment::class);
    }

    public static function markAsDeleted($jobTasks, $generalId)
    {
        foreach ($jobTasks as $jobTask) {
            if (!self::isASub($generalId, $jobTask->id)){
                $subTasks = self::getSubTasks($jobTask->id);
                if (!\is_null($subTasks)) {
                    foreach ($subTasks as $subTask) {
                        $subTask->delete();
                    }
                }
            }
        }
    }

    public static function isASub($generalId, $jobTaskContractorId)
    {
        return $generalId !== $jobTaskContractorId;
    }

    public static function getSubTasks($jobTaskId)
    {
        return BidContractorJobTask::where('job_task_id', '=', $jobTaskId)->get();
    }

}
