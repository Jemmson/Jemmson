<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StripeAccountVerification extends Model
{
    //
    protected $table = 'stripe_account_verification';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'stripe_id', 'account_id');
    }

    public static function get($accountId)
    {
        $sav = StripeAccountVerification::where('account_id', '=', $accountId)->get()->first();
        if (\is_null($sav)) {
            return new StripeAccountVerification();
        }

        return $sav;
    }

    public function updateTable($account_id, $verificationDetails)
    {
        $this->account_id = $account_id;
        $this->current_deadline = $verificationDetails['current_deadline'];
        $this->currently_due = json_encode($verificationDetails['currently_due']);
        $this->disabled_reason = $verificationDetails['disabled_reason'];
        $this->errors = json_encode($verificationDetails['errors']);
        $this->eventually_due = json_encode($verificationDetails['eventually_due']);
        $this->past_due = json_encode($verificationDetails['past_due']);
        $this->pending_verification = json_encode($verificationDetails['pending_verification']);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }

    }

}
