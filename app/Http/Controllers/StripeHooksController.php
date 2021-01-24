<?php

namespace App\Http\Controllers;

use App\StripeAccountVerification;
use App\StripeEvent;
use App\StripeExpress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\JobTask;
use App\User;

class StripeHooksController extends Controller
{
    public function hooks(Request $request)
    {
//        Log::debug('sk_test_519YZpRIX4qnobbHhhbsdPCMtc4OhFEVP6cCshbIlTSuwYnRBbpf2a230zaqkCuKbkKZYUL7zIoyHVI3wCf7B3p2y00GdnzPrEV');
//        Log::debug(dd(env('STRIPE_SECRET')));

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpoint_secret = 'whsec_OJY1Mxu5fPM86Yj4imiQCWq4o4RPBRLT';


        $sig_header = $request->headers->get('stripe-signature');

        $payload = $request->getContent();

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response([], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response([], 400);
        };

        if ($this->checkForDuplicateEvent($event)) {
            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->processPaymentIntentSucceeded($event);
                    break;
//                case 'charge.succeeded':
//                    $this->processChargeSucceeded($event);
//                    break;
//                case 'transfer.created':
//                    $this->transferCreated($event);
//                    break;
//                case 'payment.created':
//                    $this->paymentCreated($event);
//                    break;
//                case 'payment.attached':
//                    $this->paymentAttached($event);
//                    break;
//                case 'payment_method.attached':
//                    $this->paymentMethodAttached($event);
//                    break;
//                case 'payment_intent.payment_failed':
//                    $this->paymentIntentPaymentFailed($event);
//                    break;
//                case 'invoice.upcoming':
//                    $this->invoiceUpcoming($event);
//                    break;
//                case 'account.application.authorized':
//                    $this->accountApplicationAuthorized($event);
//                    break;
//                case 'capability.updated':
//                    $this->capabilityUpdated($event);
//                    break;
                case 'account.updated':
                    $this->accountUpdated($event);
                    break;
//                case 'setup_intent.succeeded':
//                    $this->setupIntentSucceeded($event);
//                    break;
//                case 'payment_intent.created':
//                case 'setup_intent.created':
//                case 'invoice.payment_succeeded':
//                    break;
//                case 'invoice.deleted':
//                case 'invoice.created':
//                    $this->invoiceCreated($event);
//                    break;
//                case 'invoice.finalized':
//                case 'invoice.marked_uncollectible':
//                case 'invoice.payment_action_required':
//                case 'invoice.payment_failed':
//                case 'invoice.sent':
//                case 'invoice.updated':
//                case 'invoice.voided':
//                    $this->saveInvoice($event);
//                    break;
                default:
                    // Unexpected event type
                    return response([], 400);
            }
        }

        return response([], 200);

    }

    public function saveInvoice($event)
    {
//        check if the invoice coming in is a customer billing invoice or an invoice from a job
        $userBilling = User::where('stripe_id', '=', $event->account_id)->get()->first();

        if (\is_null($userBilling)) {
            $userJob = User::where('customer_stripe_id', '=', $event->account_id)->get()->first();
            if (\is_null($userJob)) {
//                trigger a user does not exist exception
            } else {
//                add invoice to stripe invoices table
            }
        } else {
//            add invoice to stripe billing table
        }

    }

    public function invoiceCreated($event)
    {
        Log::debug($event);
    }

    public function processPaymentIntentSucceeded($event)
    {
        $paymentIntentId = $event->data->object['id'];

        $jobTasks = JobTask::where('payment_intent_id', '=', $paymentIntentId)->get();

        $jobId = $jobTasks->first()->job_id;

        $stripeExpress = new StripeExpress();

        $totalAmount = $event->data->object['amount'];
        $chargeId = $event->data->object['charges']['data'][0]['id'];
        $transferGroupId = $event->data->object['metadata']['transferGroupId'];

        $stripeExpress->transferFunds(
            $jobId, $jobTasks, $totalAmount, $chargeId, $transferGroupId
        );

//        payment has processed and the charge is pending
//        notify the general and the subs


    }

//    public function capabilityUpdated($request)
//    {
//        return $request;
//    }

    /*
     * https://stripe.com/docs/connect/identity-verification-api
     * - will receive an accounts object -> https://stripe.com/docs/api/accounts/retrieve
     *
     * */

    public function accountUpdated($event)
    {

        $stripeVerification = StripeAccountVerification::get($event->account);
        $stripeVerification->updateTable($event->account, $event->data->object['requirements']);

        $stripeEvent = StripeEvent::get($event->id);
        $stripeEvent->updateTable($event);

    }

//    public function accountApplicationAuthorized($request)
//    {
////        look at the response
////        ‌Your destination account needs to have at least one of the following capabilities enabled: transfers, legacy_payments
//
////        echo $request;
//
//        return $request;
//    }

//    public function setupIntentSucceeded($event)
//    {
//        // does customer exist then update otherwise create
//
//
//        // add the payment method
//
//
//    }

//    public function paymentIntentPaymentFailed($request)
//    {
//        return $request;
//    }
//
//    public function processChargeSucceeded($request)
//    {
//        return $request;
//    }
//
//    public function transferCreated($request)
//    {
//        return $request;
//    }
//
//    public function paymentCreated($request)
//    {
//        return $request;
//    }
//
//    public function paymentAttached($request)
//    {
//        return $request;
//    }
//
//    public function paymentMethodAttached($request)
//    {
//        return $request;
//    }
//
//    public function invoiceUpcoming($request)
//    {
//        return $request;
//    }

    public function checkForDuplicateEvent($event)
    {
        $eventIdentifier = StripeEvent::get($event->id);
        $newEvent = $eventIdentifier->event_id;
        $eventIdentifier->updateTable($event);
        return \is_null($newEvent);
    }

}
