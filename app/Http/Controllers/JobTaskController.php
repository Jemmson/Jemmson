<?php

namespace App\Http\Controllers;

use App\JobTask;
use App\Task;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Traits\ConvertPrices;

class JobTaskController extends Controller
{
    //

    use ConvertPrices;

    public function updateDetails(Request $request)
    {
        //        validate that all required fields exist
        try {
            $this->validate($request, [
                'taskName' => 'required',
                'startDate' => 'required',
                'jobTaskId' => 'required'
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }

        $jobTask = JobTask::find($request->jobTaskId);
        $task = Task::find($jobTask->task_id);

        self::updateStartDate($jobTask, $request->startDate);
        self::updateTaskName($task, $request->taskName);

        return self::getJobTaskForGeneral($jobTask->id, null);

    }

    public function updateTaskName($task, $taskName)
    {
        $task->name = $taskName;

        try {
            $task->save();
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
        }
    }

    public function updateStartDate($jobTask, $startDate)
    {
        $jobTask->start_date = $startDate;

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
        }
    }

    public function getJobTaskForGeneral($jobTaskId, $userId)
    {

        $jobTasks = JobTask::with(
            [
                'task',
                'images',
                'location',
                'subStatuses',
                'job',
                'job.jobStatuses',
                'taskMessages',
                'job.customer',
                'jobTaskStatuses',
                'bidContractorJobTasks',
                'bidContractorJobTasks.contractor',
                'bidContractorJobTasks.contractor.contractor'
            ])->where('id', '=', $jobTaskId)->get();
        foreach ($jobTasks as $jt) {
            $jt->cust_final_price = $this->convertToDollars($jt->cust_final_price);
            $jt->sub_final_price = $this->convertToDollars($jt->sub_final_price);
            $jt->unit_price = $this->convertToDollars($jt->unit_price);
            foreach ($jt->bidContractorJobTasks as $bidContractorJobTask) {
                $bidContractorJobTask->bid_price = $this->convertToDollars($bidContractorJobTask->bid_price);
            }
        }
        return $jobTasks;
    }


    public function updateJTLocation(Request $request)
    {

        //        validate that all required fields exist
        try {
            $this->validate($request, [
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required'
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }

        //        does the location exist
        $locationExists = self::locationExists($request);
        $jobTask = JobTask::find($request->id);
        $jobTaskId = $request->id;
        if ($locationExists) {
            if (!self::jobTaskAlreadyAssociatedToLocation($jobTask->location_id, $locationExists->id)) {
//           if so then update the id of the job to the id of the existing location
                self::updateJobTaskLocation($jobTaskId, $locationExists->id);
            }
        } else {
//        if not then create a new location and then update the job with the id of the new location
            $locationId = self::addNewLocation($request);
            self::updateJobTaskLocation($jobTaskId, $locationId);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function addNewLocation($request)
    {
        $address = strtolower($request->address_line_1);
        $address2 = empty($request->address_line_2) ? null : strtolower($request->address_line_2);
        $city = strtolower($request->city);
        $state = strtolower($request->state);
        $zip = self::formatZip(strtolower($request->zip));
        $userId = strtolower(Auth::user()->getAuthIdentifier());
        $newLocation = new Location();
        $addedLocation = $newLocation->addNewLocation($address, $address2, $city, $state, $zip, $userId);
        return $addedLocation->id;
    }

    public function updateJobTaskLocation($jobTaskId, $locationId)
    {
        $jobTask = JobTask::find($jobTaskId);
        $jobTask->location_id = $locationId;

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('jobTaskLocationError ' . $e->getMessage());
        }
    }

    public function jobTaskAlreadyAssociatedToLocation($currentJobTaskLocationId, $existingLocationId)
    {
        return $currentJobTaskLocationId == $existingLocationId;
    }

    public function locationExists($request)
    {
        $address = strtolower($request->address_line_1);
        $city = strtolower($request->city);
        $state = strtolower($request->state);
        $zip = self::formatZip(strtolower($request->zip));
        return Location::where('address_line_1', '=', $address)
            ->where('city', '=', $city)
            ->where('state', '=', $state)
            ->where('zip', '=', $zip)
            ->get()->first();
    }

    public function formatZip($zip)
    {
        return rtrim($zip, '-');
    }
}
