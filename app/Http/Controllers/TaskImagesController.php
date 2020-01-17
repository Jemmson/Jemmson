<?php

namespace App\Http\Controllers;

use App\Job;
use App\TaskImage;
use Illuminate\Http\Request;

class TaskImagesController extends Controller
{
    //

    public function getImagesNotAssociatedToATask($jobId)
    {
        return Job::where('id', '=', $jobId)->get()->first()->images()->where('job_task_id', '=', null)->get();
    }

    public function associateImagesToTasks(Request $request)
    {
        foreach ($request->imageTasks as $imageTask) {
            $taskImage = TaskImage::find($imageTask['image_id']);
            $taskImage->job_task_id = $imageTask['jobTaskId'];
            try {
                $taskImage->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }
        }
    }
}
