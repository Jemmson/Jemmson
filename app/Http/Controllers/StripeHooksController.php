<?php

namespace App\Http\Controllers;

use App\StripeExpress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\JobTask;

class StripeHooksController extends Controller
{
    //

    public function hooks(Request $request)
    {
//        Log::debug(json_encode($request));
        Log::debug($request);

        $event = $request->type;

        switch ($event) {
            case 'payment_intent.created':
                break;
            case 'payment_intent.succeeded':
                $this->processPaymentIntentSucceeded($request);
                break;
            case 'charge.succeeded':
                $this->processChargeSucceeded($request);
                break;
            case 'transfer.created':
                $this->transferCreated($request);
                break;
            case 'payment.created':
                $this->paymentCreated($request);
                break;
            case 'payment.attached':
                $this->paymentAttached($request);
                break;
            case 'payment_intent.payment_failed':
                $this->paymentIntentPaymentFailed($request);
                break;
            case 'invoice.upcoming':
                $this->invoiceUpcoming($request);
                break;
            case 'account.application.authorized':
                $this->accountApplicationAuthorized($request);
                break;
            case 'capability.updated':
                $this->capabilityUpdated($request);
                break;
            case 'account.updated':
                $this->accountUpdated($request);
                break;
        }
    }

    public function processPaymentIntentSucceeded($request)
    {
        $paymentIntentId = $request->data['object']['id'];

        $jobTasks = JobTask::where('payment_intent_id', '=', $paymentIntentId)->get();

        $jobId = $jobTasks->first()->job_id;

        $stripeExpress = new StripeExpress();

        $totalAmount = $request->data['object']['amount'];
        $chargeId = $request->data['object']['charges']['data'][0]['id'];
        $transferGroupId = $request->data['object']['metadata']['transferGroupId'];
        $stripeExpress->transferFunds(
            $jobId, $jobTasks, $totalAmount, $chargeId, $transferGroupId
        );

//        payment has processed and the charge is pending
//        notify the general and the subs


    }

    public function capabilityUpdated($request)
    {
        return $request;
    }

    public function accountUpdated($request)
    {
        return $request;
    }

    public function paymentIntentPaymentFailed($request)
    {
        return $request;
    }

    public function processChargeSucceeded($request)
    {
        return $request;
    }

    public function transferCreated($request)
    {
        return $request;
    }

    public function paymentCreated($request)
    {
        return $request;
    }

    public function paymentAttached($request)
    {
        return $request;
    }

    public function invoiceUpcoming($request)
    {
        return $request;
    }

    public function accountApplicationAuthorized($request)
    {
        return $request;
    }

}
