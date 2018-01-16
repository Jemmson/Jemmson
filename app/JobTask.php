<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobTask extends Model
{
    protected $table = "job_task";
    protected $fillable = ['*'];

    /**
     * Get the task this bid belongs to
     *
     * @return Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
