<?php

namespace App\Http\Controllers;

use App\JobTask;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class JobTaskController extends Controller
{
    //

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
        $jobTask = JobTask::find($request->jobTaskId);
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
