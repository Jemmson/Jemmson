<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Task;
use App\Customer;
use App\Contractor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Validator;

class JobController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('further.info', ['only' => 'edit']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Job::all();
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'agreed_start_date' => 'required',
            'agreed_end_date' => 'required',
            'bid_price' => 'required'
        ]);
        Log::info($request->all());
        if ($validator->fails()) {
            return response()->json(['error' => 'Missing fields'], 400);
        }
        
        $job = Job::create($request->all());

        return response()->json($job, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Job $job
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
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        $contractor = Contractor::find($job->contractor_id)->get();
        if (Customer::find($job->customer_id) == null) {
            $customer = "[]";
        } else {
            $customer = Customer::find($job->customer_id)->get();
        }
        $customerUserData = User::find(1)->where('id', '=', $job->customer_id)->get();
        $tasks = $job->tasks()->get();
        $userType = Auth::user()->usertype;
        return view('jobs.edit_job',
            compact('job', 'contractor', 'customer', 'tasks', 'userType', 'customerUserData'));
    }

    public function updateJobDate(Request $request)
    {
        $dateType = $request->params["dateType"];
        $job = Job::find(intval($request->params["id"]));
        $date = new Carbon($request->params["date"]);
        $job->$dateType = $date;
        $job->save();
        return 'Date Is Saved';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $job->update($request->all());

        return response()->json($job, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json(null, 204);
    }

    public function action(Request $request)
    {
        $job = Job::find($request->job_id);

        if ($job == null) {
            return ['status' => false, 'error' => 'Resource not found'];
        }

        // do required action
        switch ($request->action) {
            case 'accept':
                $job->acceptJob();
                break;
            case 'approve':
                $job->approveJob();
                break;
            case 'decline':
                $job->declineJob();
                break;
            default: 
                return response()->json(['error' => 'action required'], 400);       
                break;
        }

        return response()->json($job->jobActions(), 200);        
    }
}
