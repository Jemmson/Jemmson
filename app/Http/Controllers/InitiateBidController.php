<?php

namespace App\Http\Controllers;

use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\SMS\Facades\SMS;
use App\User;
use App\Job;
use App\Customer;
use App\Mail\PasswordlessBidPageLogin;
use Illuminate\Support\Facades\DB;
use App\Notifications\BidInitiated;
use App\Services\SanatizeService;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use RuntimeException;


class InitiateBidController extends Controller
{
    /**
     * Returning the initial page to initiate the view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('initiate-bid');
    }

    /**
     * Initiate a bid
     *
     * @param Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send(Request $request)
    {

        $this->validate($request, [
            'phone' => 'required|min:10|max:14',
            'customerName' => 'required',
            'jobName' => 'nullable|regex:/^[a-zA-Z0-9 .\-#,]+$/i'
        ]);

        $contractor = Auth::user()->contractor()->first();

        if (!$contractor->canCreateNewJob()) {
            return response()->json(
                [
                    'message' => 'No more free Jobs left.',
                    'errors' => ['no_free_jobs' => 'No more free Jobs left']
                ], 422);
        }

        $customerName = $request->customerName;
        $phone = SanatizeService::phone($request->phone);
        $customer = User::checkIfUserExistsByPhoneNumber($phone);

        if (empty($customer)) {
            $customer = Customer::createNewCustomer($phone, $customerName);
        }

        // create a job name if one does not exist
        $job = new Job();
        $jobName = $job->jobName($request->jobName);

        // create a bid
        $job = $job->createBid($customer->id, $jobName, Auth::user()->id);

        if (!$job) {
            return redirect()->back()->with(
                'error',
                'Job could not be created. Please try initiating the bid again'
            );
        }
        $job_id = $job->id;

        // with error
        if ($job_id == null) {
            // TODO: delete job/token if only one was created
            return redirect()->back()->with(
                'error',
                'Sorry couldn\'t create the bid, please try again.'
            );
        }

        $contractor->subtractFreeJob();

        $customer->notify(new BidInitiated($job, $customer));

        $request->session()->flash('status', 'Your bid was created');

        return "Bid was created";

    }
}
