<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;

class TaskImagesController extends Controller
{
    //

    public function getImagesNotAssociatedToATask($jobId)
    {
        return Job::where('id', '=', $jobId)->get()->first()->images()->where('job_task_id', '=', null)->get();
    }
}
