<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobTask extends Model
{
    protected $table = "job_task";
    protected $fillable = ['job_id', 'task_id'];

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
        return $this->belongsTo(User::class)->with('contractor');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function updateLocation($request)
    {
        if ($this->location_id === null) {
            $location = new Location();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        }
        
        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch(\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }
    }


    public function updateStatus($status)
    {
        if (!$this->updatable($status)) {
            return false;
        }

        $this->status = $status;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating JobStatus Status: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Can the status be changed to what its
     * trying to be changed to
     *
     * @param string $status
     * @return bool
     */
    public function updatable(string $status) 
    {
        switch ($status) {
            case 'bid_task.customer_sent_payment':
                return $this->isPayable();
                break;
            default:
                return true; // TODO: testing, should be false
                break;
        }
    }

    private function isPayable()
    {
        return $this->status === 'bid_task.finished_by_general' || $this->status === 'bid_task.approved_by_general';
    }
}
