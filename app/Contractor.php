<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contractor extends Model
{
    protected $fillable = [
      'user_id',
      'email_method_of_contact',
      'address_line_1',
      'address_line_2',
      'city',
      'state',
      'zip',
      'company_logo_name',
      'sms_method_of_contact',
      'phone_method_of_contact',
      'phone_number',
      'company_name',
    ];

    //
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function addContractorToBidForJobTable($contractorId, $taskId)
    {

        DB::table('contractor_bid_task')->insert(
            ['contractor_id' => $contractorId, 'task_id' => $taskId]
        );

    }

    public function checkIfContractorSetBidForATask($contractorId, $taskId)
    {
        if (empty(DB::table('contractor_bid_task')
            ->select('task_id')
            ->where('contractor_id', '=', $contractorId)
            ->where('task_id', '=', $taskId)
            ->get()[0])) {
            return true;
        } else {
            return false;
        }
    }
}
