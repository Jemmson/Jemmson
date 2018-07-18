<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\BidContractorJobTask;

use Log;


class JobTask extends Model
{
    use SoftDeletes;
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

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function contractor()
    {
        return $this->belongsTo(User::class)->with('contractor');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class, 'job_task_id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class, 'job_task_id');
    }

    /**
     * 
     *
     * @param String $id stripe transfer id
     * @return void
     */
    public function paid(String $id)
    {
        if ($id === null || $id === '' || $id === ' ') {
            return;
        } 

        $this->stripe_transfer_id = $id;
        $this->status = __("bid_task.customer_sent_payment");

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update JobTask: ' . $e->getMessage());
        }
    }

    /**
     * 
     *
     * @param String $id
     * @return void
     */
    public function setStripeTransferId(String $id)
    {
        if ($id === null || $id === '' || $id === ' ') {
            return;
        } 

        $this->stripe_transfer_id = $id;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update JobTask: ' . $e->getMessage());
        }
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
            return $location;
        } catch(\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
            return $e;
        }
    }

    public function toggleStripe() {
        $this->stripe = $this->stripe ? false : true;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Toggle Stripe: ' . $e->getMessage());
            return false;
        }
        return true;
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

    /**
     *
     * @param String $msg
     * @return bool
     */
    public function setDeclinedMessage(String $msg)
    {
        $this->declined_message = $msg;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Set Declined Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function resetDeclinedMessage()
    {
        $this->declined_message = null;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Reset Declined Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    private function isPayable()
    {
        return $this->status === 'bid_task.finished_by_general' || $this->status === 'bid_task.approved_by_general';
    }
}
