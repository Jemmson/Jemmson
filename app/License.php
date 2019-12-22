<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    //
    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public static function addLicenses($licenses, $user)
    {
        if (!empty($licenses)) {
            foreach($licenses[0] as $license){
                $l = new \App\License();
                $l->contractor_id = $user->id;
                $l->name = $license['name'];
                $l->number = $license['number'];
                $l->type = $license['type'];
                $l->state = $license['state'];

                try {
                    $l->save();
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'code' => $e->getCode()
                    ], 200);
                }

            }
        }
    }
}
