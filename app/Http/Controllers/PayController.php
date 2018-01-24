<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;

use App\Job;
use App\Task;
use App\JobTask;
use App\User;

use App\Notifications\CustomerPaidForTask;

class PayController extends Controller
{
    
    /**
     * Pay for individual task in bid
     *
     * @param Request $request
     * @return Array
     */
    public function payForTask(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $task = Task::find($request->id);
        $jobTask = $task->jobTask()->first();
        
        // contractor to pay
        $contractor = User::find($jobTask->contractor_id);
        
        // update relevant columns
        $jobTask->status = __('bid_task.customer_sent_payment');


        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Update JobTask|Task - Pay For Task: ' . $e->getMessage());
            return response()->json(["message"=> "Error Trying This Action", "errors"=>["error" => $e->getMessage()]], 422);
        }

        // TODO: stripe payment

        $contractor->notify(new CustomerPaidForTask($task));

        return response()->json(["message"=>"Success"], 200);
    }

}
