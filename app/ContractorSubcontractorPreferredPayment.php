<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorSubcontractorPreferredPayment extends Model
{
    //
    protected $table = 'contractor_subcontractor_preferred_payment';
    protected $guarded = [];
    use SoftDeletes;

//    public function contractor()
//    {
//        return $this->belongsTo(Contractor::class);
//    }
//
//    public function location()
//    {
//        return $this->hasOne(Location::class, 'id', 'location_id');
//    }
//
//    public function jobTask()
//    {
//        return $this->hasMany(BidContractorJobTask::class, 'job_task_id');
//    }

    /**
     * Get the job_task this bid belongs to
     *
     * @return Task
     */
    public function bidContractorJobTask()
    {
        return $this->belongsTo(BidContractorJobTask::class);
    }

}
