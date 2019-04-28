<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Notifications\Messages\NexmoMessage;
use Log;
//use Zend\Diactoros\Request;
use Illuminate\Http\Request;
//use Carbon\Carbon;
//\Illuminate\Support\Carbon::

class Task extends Model
{

    use Notifiable;

    protected $fillable = [
        'name',
        'standard_task_id',
        'contractor_id',
        'proposed_cust_price',
        'average_cust_price',
        'proposed_sub_price',
        'average_sub_price',
        'qtyUnit',
        'sub_instructions',
        'customer_instructions'
    ];

    public function createTask($request)
    {
        $this->name = strtolower($request->taskName);
        $this->contractor_id = $request->contractorId;
        $this->proposed_cust_price = $request->taskPrice;
        $this->proposed_sub_price = $request->subTaskPrice;
        $this->qtyUnit = $request->qtyUnit;
        $this->sub_instructions = $request->sub_message;
        $this->customer_instructions = $request->customer_message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't add/update task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public static function validate_new_task_input(Request $request)
    {
        $request->validate([
            'taskName' => 'required|regex:/^[a-zA-Z0-9 .\-#,]+$/i',
            'taskPrice' => 'required|numeric',
            'subTaskPrice' => 'required|numeric',
            'start_when_accepted' => 'required',
            'start_date' => 'required_if:start_when_accepted,false|date|after:yesterday',
            'qty' => 'numeric',
            'qtyUnit' => 'nullable|string'
        ]);
    }

//    public function update_existing_standard_task_add_to_jobTask_table(Request $request) {
//        // find the existing task and update the standard task table
//        // add task to job task table
//        $this->updateTask($request);
//        $jobTask = new JobTask;
//        $jobTask->createJobTask($request, $request->taskId);
//    }


    public static function create_task_input_array(Request $request)
    {
        return [
            'taskName' => $request->taskName,
            'taskPrice' => $request->taskPrice,
            'subTaskPrice' => $request->subTaskPrice,
            'start_when_accepted' => $request->start_when_accepted,
            'start_date' => $request->start_date,
            'qty' => $request->qty,
            'qtyUnit' => $request->qtyUnit,
            'updateTask' => $request->updateTask,
            'createNew' => $request->createNew
        ];
    }

    public function Jobs()
    {
        return $this->belongsToMany('App\Job')
            ->withPivot(
                'contractor_id',
                'status',
                'cust_final_price',
                'sub_final_price'
            )
            ->withTimestamps();
    }

    public function Contractors()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(
                'base_price'
            )
            ->with('contractor')
            ->withTimestamps();
    }

    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class);
    }

    public function jobTask()
    {
        return $this->hasOne(JobTask::class, 'task_id', 'id');
    }

    public function updateStatus($status)
    {
        $jobTask = $this->jobTask()->first();
        $jobTask->status = $status;

        try {
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Update Job Task: ' . $e->getMessage());
        }
    }

    public function updateTask($request = [], $options = null)
    {
        // standard task column = new column value
        $this->name = $request->taskName;
        $this->contractor_id = $request->contractorId;
        $this->proposed_cust_price = $request->taskPrice;
        $this->proposed_sub_price = $request->subTaskPrice;
        $this->qtyUnit = $request->qtyUnit;
        $this->sub_instructions = $request->sub_message;
        $this->customer_instructions = $request->customer_message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't update task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public function isTaskAQBLineItem($itemId)
    {
        return $itemId != '';
    }



    public static function getBidPrices($jobId)
    {
//         $bidPrices = DB::select("select 
//                         bid_contractor_job_task.id,
//                         bid_contractor_job_task.contractor_id, 
//                         bid_contractor_job_task.task_id, 
//                         bid_contractor_job_task.bid_price,
//                       from 
//                         contractors 
//                       inner join 
//                         bid_contractor_job_task 
//                       on 
//                         bid_contractor_job_task.contractor_id=contractors.id 
//                       where 
//                         bid_contractor_job_task.job_id=$jobId");

//         $contractorNames = [];
// //
//         foreach ($bidPrices as $bidPrice) {
//             $contractorName = DB::select("select
//                         users.name
//                         from users
//                         inner join contractors
//                         on users.id = contractors.user_id
//                         where contractors.id = $bidPrice->contractor_id");
//             array_push($contractorNames, $contractorName);
//         }

//         $bidPriceLength = sizeof($bidPrices);
//         for ($i = 0; $i < $bidPriceLength; $i++) {
//             $bidPrices[$i]->contractorName = $contractorNames[$i];
//         }

//         $bidPrices = json_encode($bidPrices);

//        return $bidPrices;

    }
}
