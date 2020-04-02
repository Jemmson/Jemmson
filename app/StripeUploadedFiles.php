<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StripeUploadedFiles extends Model
{
    //
    protected $table = 'stripe_uploaded_files';
    protected $guarded = [];

    public function stripeAccountVerification()
    {
        return $this->belongsTo(StripeAccountVerification::class, 'account_id', 'account_id');
    }

    public function create($createdDocument, $accountId)
    {
        $this->account_id = $accountId;
        $this->file_id = $createdDocument->current_deadline;
        $this->filename = $createdDocument->filename;
        $this->links_url = $createdDocument->links->url;
        $this->purpose = $createdDocument->purpose;
        $this->size = $createdDocument->size;
        $this->title = $createdDocument->title;
        $this->type = $createdDocument->type;
        $this->url = $createdDocument->url;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "",
                "errors" => ["error" => [$e->getMessage()]]], );
        }
        
        
    }

}
