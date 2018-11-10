<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'user_id',
        'notes',
        'email_method_of_contact',
        'phone_method_of_contact',
        'sms_method_of_contact',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'customer_id', 'user_id');
    }
    
    public function contractors()
    {
        return $this->belongsToMany(Contractor::class);
    }
    
    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    private function updateJoblocations()
    {
        Job::where('customer_id', $this->user()->first()->id)->update(['location_id' => $this->location_id]);
    }

    public function updateTaskLocations()
    {
        $jobs = Job::where('customer_id', $this->user()->first()->id)->get();
        foreach ($jobs as $job) {
            $jobTasks = $job->jobTasks()->get();
            foreach($jobTasks as $jobTask) {
                $jobTask->location_id = $job->location_id;
                $jobTask->save();
            }
        }
    }

    public function updateLocation($request)
    {
        $newLocation = false;
        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->user()->first()->id;
            $location->default = true;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
            $newLocation = true;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
            if ($newLocation) {
                $this->updateJobLocations();
                $this->updateTaskLocations();
            }
        } catch(\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }



    }

    // TODO: understand where an intermidate table relates to two other tables
    // TODO: define relationship where table references itself
    // TODO: I need to be able to have a contractor reference many contractors
}
