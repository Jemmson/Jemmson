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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $bids = Task::getBidPrices($job->id);
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
            compact('job', 'contractor', 'customer', 'tasks', 'userType', 'customerUserData', 'bids'));
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
            Log::error('Error Saving Job: ' . $e->getMessage());
        }
        return redirect()->back()->with('success', "Job Saved Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        //
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
                return ['status' => $job->acceptJob()];
                break;
            case 'approve':
                return ['status' => $job->approveJob()];
                break;
            case 'decline':
                return ['status' => $job->declineJob()];
                break;
            default:
                return ['status' => false, 'error' => 'No action defined'];
                break;
        }

        return ['status' => false, 'error' => 'Whoops! something went wrong!'];

    }
}
