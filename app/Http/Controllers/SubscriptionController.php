<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{

    public function getPaymentIntent()
    {
        return Auth::user()->createSetupIntent();
    }

    public function setPaymentMethod(Request $request)
    {
        $plans = $this->plans();

        try {
            Auth::user()->newSubscription('default', $plans[$request->selectedPlan])->create($request->paymentMethod);
            Auth::user()->plan = $request->selectedPlan;
            Auth::user()->save();
            return response()->json([
                'success' => 'Your Plan Has Been Successfully Updated',
                'currentPlan' => $request->selectedPlan
            ], 200);
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "We Were Unable To Create Your New Plan At This Time. Please Try Again",
                "error" => [$e->getMessage()]], 200);
        }

    }

    public function changePlan(Request $request)
    {
        $plans = $this->plans();

        try {
            if (
                Auth::user()->subscription('default')->onGracePeriod()
                && Auth::user()->subscription('default')->stripe_plan === $plans[$request->selectedPlan]
            ) {
                Auth::user()->subscription('default')->resume();
                return response()->json([
                    'success' => 'Your Plan Has Been Successfully Resumed',
                    'currentPlan' => $request->selectedPlan
                ], 200);
            } else {
                Auth::user()->subscription('default')->swap($plans[$request->selectedPlan]);
                Auth::user()->plan = $request->selectedPlan;
                Auth::user()->save();
                return response()->json([
                    'success' => 'Your Plan Has Been Successfully Changed',
                    'currentPlan' => $request->selectedPlan
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "We Were Unable To Change Your Plan. Please Try Again",
                "error" => [$e->getMessage()]], 200);
        }

    }

    public function cancelPlan()
    {
        try {
            Auth::user()->subscription('default')->cancel();;
            Auth::user()->plan = null;
            Auth::user()->save();
            return response()->json([
                'success' => 'Your Plan Has Been Successfully Canceled'
            ], 200);
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "We Were Unable To Cancel Your Plan. Please Try Again",
                "error" => [$e->getMessage()]], 200);
        }
    }
    
    private function plans () {
        return [
            'Monthly Plan' => env('STRIPE_MONTHLY_PLAN'),
            'Yearly Plan' => env('STRIPE_YEARLY_PLAN')
        ];
    }

}
