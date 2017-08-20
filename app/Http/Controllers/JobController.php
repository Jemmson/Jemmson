<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        $contractor = User::find($job->contractor_id);
        return view('jobs.job')->with(['job' => $job, 'contractor' => $contractor->name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        $contractor = User::find($job->contractor_id);
        return view('jobs.edit_job')->with(['job' => $job, 'contractor' => $contractor->name]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $this->validate($request, [
          'agreed_start_date' => 'required',
          'agreed_end_date' => 'required',
          'bid_price' => 'required',
        ]);
        $job->agreed_start_date = $request->agreed_start_date;
        $job->agreed_end_date = $request->agreed_end_date;
        $job->bid_price = $request->bid_price;

        try {
          $job->save();
        } catch (\Exception $e) {
          Log::error('Error Saving Job: '. $e->getMessage());
        }
        return redirect()->back()->with('success', "Job Saved Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        //
    }
}
