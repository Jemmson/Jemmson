<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Customer;
use App\ContractorCustomer;
use App\Location;
use Illuminate\Support\Facades\Auth;

class Contractor extends Model
{
//    protected $fillable = [
//        'user_id',
//        'company_name',
//        'company_logo_name',
//        'email_method_of_contact',
//        'sms_method_of_contact',
//        'phone_method_of_contact',
//    ];

    protected $guarded = [];

    //

    public function jobs()
    {
        return $this->hasMany(Job::class, 'contractor_id', 'user_id');
    }

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'contractor_id', 'user_id');
    }

    public function time()
    {
        return $this->hasMany(Time::class, 'contractor_id', 'user_id');
    }

    public function numberOfJobsLeft()
    {
        return $this->free_jobs;
    }

    public function hasMoreFreeJobs()
    {
        return $this->free_jobs > 0;  // means there are more free jobs that exist for the contractor
    }

    public function isSubscribed()
    {

        return $this->user->current_billing_plan !== null; // means the contractor has subscribed

    }

    public function usesAccountingSoftware($softwareName)
    {
        $this->accounting_software = $softwareName;
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canCreateNewJob()
    {
        if ($this->isSubscribed()) {
            return true;
        } else if ($this->hasMoreFreeJobs()) {
            $this->subtractFreeJob();
            return true;
        } else {
            return false;
        }
    }

    public function checkAccountingSoftware()
    {
        return $this->accounting_software;
    }

    public function subtractFreeJob()
    {
        if ($this->free_jobs <= 0) {
            return true;
        }
        $this->free_jobs -= 1;
        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Subtract Free Job: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function customers()
    {
        return $this->belongsToMany(
            'App\Customer',
            'contractor_customer',
            'contractor_user_id',
            'customer_user_id'
        );
    }

    public function stripeExpress()
    {
        return $this->hasOne(StripeExpress::class, 'contractor_id', 'user_id');
    }

    public function tasks()
    {
        return $this
            ->belongsToMany(Task::class, 'contractor_id', 'user_id')->withPivot('base_price')
            ->withTimestamps();
    }

    /**
     * Get all tasks sent to the
     * current contractor as bids
     *
     * @return void
     */
    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class, 'contractor_id', 'user_id');
    }

    public function addContractorToBidForJobTable($subcontractorId, $jobTaskId, $taskId)
    {
        $proposedBidPrice = Task::find($taskId)->proposed_sub_price;
        // TODO: update this
        DB::table('bid_contractor_job_task')->insert(
            ['contractor_id' => $subcontractorId, 'job_task_id' => $jobTaskId, "bid_price" => $proposedBidPrice]
        );

    }

    public function checkIfContractorSetBidForATask($subcontractorId, $jobTaskId)
    {
        if (empty(DB::table('bid_contractor_job_task')
            ->select('job_task_id')
            ->where('contractor_id', '=', $subcontractorId)
            ->where('job_task_id', '=', $jobTaskId)
            ->get()[0])) {
            return true;
//            DB::table('bid_contractor_job_task')->select('job_task_id')->where('contractor_id', '=', 3)->where('job_task_id', '=', 3)->get()[0];
        } else {
            return false;
        }
    }

    public function updateLocation($request)
    {

        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->user_id;
            $location->default = true;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
            $location->area = $request->city;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }


    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function addLocation($qbCustomerData)
    {
        $location = new Location();

        if (!is_null($qbCustomerData[0]->BillAddr)) {
            $location->address_line_1 = $qbCustomerData[0]->BillAddr->Line1;
            $location->address_line_2 = $qbCustomerData[0]->BillAddr->Line2;
            $location->city = $qbCustomerData[0]->BillAddr->City;
            $location->state = $qbCustomerData[0]->BillAddr->CountrySubDivisionCode;
            $location->zip = $qbCustomerData[0]->BillAddr->PostalCode;
            $location->lat = $qbCustomerData[0]->BillAddr->Lat;
            $location->long = $qbCustomerData[0]->BillAddr->Long;
        }

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $location->id;
    }

    //TODO: if location is saved from QB data then it is not saved again once a customer registers

    public function addCustomerToUserTable($qbCustomerData, $locationId, $phone)
    {
        $customer = new \App\User();
        $customer->name = $qbCustomerData[0]->FullyQualifiedName;
        $customer->location_id = $locationId;

        if (!is_null($qbCustomerData[0]->PrimaryEmailAddr)) {
            $customer->email = $qbCustomerData[0]->PrimaryEmailAddr->Address;
        }

        $customer->usertype = 'customer';
        $customer->phone = $phone;

        $customer->first_name = $qbCustomerData[0]->GivenName;
        $customer->last_name = $qbCustomerData[0]->FamilyName;
        $customer->password = bcrypt(rand(100000, 999999) . 'gibberishslksdlkdslksdslkdsdlk');
        $customer->password_updated = false;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        return $customer->id;
    }

    public function addCustomerToCustomerTable($qbCustomerData, $locationId, $user_id)
    {
        $customer = new Customer();
        $customer->user_id = $user_id;
        $customer->location_id = $locationId;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
//        return $customer->id;
    }

    public function associateContractorToCustomerTable($user_id, $contractorId, $quickbooksId)
    {
        $customer = new ContractorCustomer();
        $customer->contractor_user_id = $contractorId;
        $customer->customer_user_id = $user_id;
        $customer->quickbooks_id = $quickbooksId;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function addUserIdToLocationsTable($user_id, $locationId)
    {
        $location = \App\Location::find($locationId);
        $location->user_id = $user_id;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function formatPhone($phone)
    {
        $phoneString = '';
        for ($i = 0; $i < strlen($phone); $i++) {
            if ($phone[$i] == '1') {
                $phoneString = $phoneString . 1;
            } else if ($phone[$i] == '2') {
                $phoneString = $phoneString . 2;
            } else if ($phone[$i] == '3') {
                $phoneString = $phoneString . 3;
            } else if ($phone[$i] == '4') {
                $phoneString = $phoneString . 4;
            } else if ($phone[$i] == '5') {
                $phoneString = $phoneString . 5;
            } else if ($phone[$i] == '6') {
                $phoneString = $phoneString . 6;
            } else if ($phone[$i] == '7') {
                $phoneString = $phoneString . 7;
            } else if ($phone[$i] == '8') {
                $phoneString = $phoneString . 8;
            } else if ($phone[$i] == '9') {
                $phoneString = $phoneString . 9;
            } else if ($phone[$i] == '0') {
                $phoneString = $phoneString . 0;
            }
        }
        return $phoneString;
    }

    public function firstOrCreateAccountingSoftwareCustomer($accountingSoftware,
                                                            $contractorId,
                                                            $customerName,
                                                            $phone,
                                                            $qbId = null)
    {

        $phone = $this->formatPhone($phone);
        if ($accountingSoftware == 'quickBooks') {
            $qb = new Quickbook();
            // check quickbooks customer table
            if (!empty($qbId)) {
                $qbCustomerData = $qb->getLatestCustomerDataFromQB($qbId, $contractorId);
                $locationId = $this->addLocation($qbCustomerData);
                $user_id = $this->addCustomerToUserTable($qbCustomerData, $locationId, $phone);
                $this->addUserIdToLocationsTable($user_id, $locationId);
                $this->addCustomerToCustomerTable(
                    $qbCustomerData, $locationId, $user_id);
                $this->associateContractorToCustomerTable($user_id, $contractorId, $qbId);
                CustomerNeedsUpdating::addEntryToCustomerNeedsUpdatingIfNeeded(
                    $contractorId,
                    $user_id,
                    $qbId
                );
            }
        }

        return User::find($user_id);
    }

    public function getGeneralContractorsCompanyName()
    {
        return \App\User::find(Auth::user()->getAuthIdentifier())->contractor()->get()->first()->company_name;
    }

    public function getAllContractorsExceptTheGeneralContractor($company_name, $generalContractorsCompanyName)
    {
        return \App\Contractor::where('company_name', 'like', "$company_name%")
            ->where('company_name', '!=', "$generalContractorsCompanyName")->get();
    }

    public function subsWithPhoneNumberAndEmail($subs)
    {
        $subsArray = [];
        $subInfo = '';
        foreach ($subs as $sub) {

            $c = $sub->user()->get(['id', 'name', 'email', 'phone', 'first_name', 'last_name'])->first();

            $qbc = QuickbooksContractor::where('given_name', '=', $c->first_name)->
            where('family_name', '=', $c->last_name)->
            where('contractor_id', '=', Auth::user()->getAuthIdentifier())->
            get()->first();


            if (empty($qbc)) {
                $qbc = QuickbooksContractor::where('given_name', '=', $c->first_name)->
                where('family_name', '=', $c->last_name)->
                get()->first();
            }

            if (empty($qbc)) {
                $subInfo = [
                    'id' => $sub->user_id,
                    'contractor_id' => $sub->user_id,
                    'name' => $c->name,
                    'contractor' => [
                        'company_name' => $sub->company_name
                    ],
                    'phone' => $c->phone,
                    'email' => $c->email,
                    'quickbooks_id' => '',
                    'given_name' => '',
                    'family_name' => '',
                    'first_name' => $c->first_name,
                    'last_name' => $c->last_name
                ];
                array_push($subsArray, $subInfo);
            } else {
                $subInfo = [
                    'id' => $sub->user_id,
                    'contractor_id' => $sub->user_id,
                    'name' => $c->name,
                    'contractor' => [
                        'company_name' => $sub->company_name
                    ],
                    'phone' => $c->phone,
                    'email' => $c->email,
                    'quickbooks_id' => $qbc->quickbooks_id,
                    'given_name' => $qbc->given_name,
                    'family_name' => $qbc->family_name,
                    'first_name' => $c->first_name,
                    'last_name' => $c->last_name
                ];
            }
            array_push($subsArray, $subInfo);
        }

        return $subsArray;
    }

    public function getSubsThatHaveBeenUsedByLineItemInOrderOfUsedMost()
    {
        // TODO:: get all subs used for a given line item in the order of those used most per given line item
    }

    public function getFavoritedSubsForGivenLineItem()
    {
        // TODO:: get all subs for a given line item that have been favorited by the general contractor
    }

    public function getAllCompaniesInQuickBookContractorsByCompanyName($companyName)
    {
        return \App\QuickbooksContractor::where('company_name', 'like', "$companyName%")->orderBy('company_name', 'asc')->get();
    }


    public function filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, $contractorId)
    {
        $subs = [];


        // TODO: need to advance c to the next name once found. must include all companies that have the
        // TODO: same company names but different given and family names.


        for ($i = 0; $i < count($allCompanies); $i++) {
            $subExists = false;
            $counter = 0;
            $start = $i;
            for ($j = $i; $j < count($allCompanies); $j++) {
                $counter++;
                if ($allCompanies[$i]->company_name == $allCompanies[$j]->company_name) {
                    if ($allCompanies[$j]->contractor_id == $contractorId) {
                        $subExists = true;
                        $contractorSub = $allCompanies[$j];

                        if ($contractorSub->family_name == "NULL") {
                            $name = $contractorSub->given_name;
                        } else if ($contractorSub->given_name == "NULL") {
                            $name = $contractorSub->family_name;
                        } else {
                            $name = $contractorSub->given_name . " " . $contractorSub->family_name;
                        }
                        array_push($subs, [
                            'id' => '',
                            'contractor_id' => $contractorSub->contractor_id,
                            'quickbooks_id' => $contractorSub->quickbooks_id,
                            'name' => $name,
                            'given_name' => $contractorSub->given_name,
                            'family_name' => $contractorSub->family_name,
                            'last_name' => '',
                            'first_name' => '',
                            'contractor' => [
                                'company_name' => $contractorSub->company_name
                            ],
                            'phone' => $contractorSub->primary_phone,
                            'email' => $contractorSub->primary_email_addr,
                        ]);

                    }

                } else {
                    $i = $j;
                    break;
                }

                if ($counter == count($allCompanies)) {
                    $i = $j;
                }

                if ($j == count($allCompanies)) {
                    $i = $j;
                }
            }
            if (!$subExists) {
                if ($allCompanies[$start]->family_name == "NULL") {
                    $name = $allCompanies[$start]->given_name;
                } else if ($allCompanies[$start]->given_name == "NULL") {
                    $name = $allCompanies[$start]->family_name;
                } else {
                    $name = $allCompanies[$start]->given_name . " " . $allCompanies[$start]->family_name;
                }
                array_push($subs, [
                    'id' => '',
                    'contractor_id' => $allCompanies[$start]->contractor_id,
                    'quickbooks_id' => $allCompanies[$start]->quickbooks_id,
                    'name' => $name,
                    'given_name' => $allCompanies[$start]->given_name,
                    'family_name' => $allCompanies[$start]->family_name,
                    'last_name' => '',
                    'first_name' => '',
                    'contractor' => [
                        'company_name' => $allCompanies[$start]->company_name
                    ],
                    'phone' => $allCompanies[$start]->primary_phone,
                    'email' => $allCompanies[$start]->primary_email_addr,
                ]);
            }
        }


//            foreach ($allCompanies as $c) {
//
//                $subExists = false;
//                $contractorSub = new QuickbooksContractor();
//                foreach ($allCompanies as $s) {
//                    if ($c->company_name == $s->company_name) {
//                        if ($c->contractor_id == $contractorId) {
//                            $subExists = true;
//                            $contractorSub = $s;
//
//                            if ($contractorSub->family_name == "NULL") {
//                                $name = $contractorSub->given_name;
//                            } else if ($contractorSub->given_name == "NULL") {
//                                $name = $contractorSub->family_name;
//                            } else {
//                                $name = $contractorSub->given_name . " " . $contractorSub->family_name;
//                            }
//                            array_push($subs, [
//                                'id' => '',
//                                'contractor_id' => $contractorSub->contractor_id,
//                                'quickbooks_id' => $contractorSub->quickbooks_id,
//                                'name' => $name,
//                                'given_name' => $contractorSub->given_name,
//                                'family_name' => $contractorSub->family_name,
//                                'last_name' => '',
//                                'first_name' => '',
//                                'contractor' => [
//                                    'company_name' => $contractorSub->company_name
//                                ],
//                                'phone' => $contractorSub->primary_phone,
//                                'email' => $contractorSub->primary_email_addr,
//                            ]);
//
//                        }
//                    } else {
//                        break;
//                    }
//                }

//                if (!$subExists) {
//                    if ($c->family_name == "NULL") {
//                        $name = $c->given_name;
//                    } else if ($c->given_name == "NULL") {
//                        $name = $c->family_name;
//                    } else {
//                        $name = $c->given_name . " " . $c->family_name;
//                    }
//                    array_push($subs, [
//                        'id' => '',
//                        'contractor_id' => $contractorSub->contractor_id,
//                        'quickbooks_id' => $contractorSub->quickbooks_id,
//                        'name' => $name,
//                        'given_name' => $c->given_name,
//                        'family_name' => $c->family_name,
//                        'last_name' => '',
//                        'first_name' => '',
//                        'contractor' => [
//                            'company_name' => $c->company_name
//                        ],
//                        'phone' => $c->primary_phone,
//                        'email' => $c->primary_email_addr,
//                    ]);
//                }


//            if ($subExists) {
//
//                if ($contractorSub->family_name == "NULL") {
//                    $name = $contractorSub->given_name;
//                } else if ($contractorSub->given_name == "NULL") {
//                    $name = $contractorSub->family_name;
//                } else {
//                    $name = $contractorSub->given_name . " " . $contractorSub->family_name;
//                }
//                array_push($subs, [
//                    'id' => '',
//                    'contractor_id' => $contractorSub->contractor_id,
//                    'quickbooks_id' => $contractorSub->quickbooks_id,
//                    'name' => $name,
//                    'given_name' => $contractorSub->given_name,
//                    'family_name' => $contractorSub->family_name,
//                    'last_name' => '',
//                    'first_name' => '',
//                    'contractor' => [
//                        'company_name' => $contractorSub->company_name
//                    ],
//                    'phone' => $contractorSub->primary_phone,
//                    'email' => $contractorSub->primary_email_addr,
//                ]);
//
//            }
//            else {
//
//                if ($c->family_name == "NULL") {
//                    $name = $c->given_name;
//                } else if ($c->given_name == "NULL") {
//                    $name = $c->family_name;
//                } else {
//                    $name = $c->given_name . " " . $c->family_name;
//                }
//                array_push($subs, [
//                    'id' => '',
//                    'contractor_id' => $contractorSub->contractor_id,
//                    'quickbooks_id' => $contractorSub->quickbooks_id,
//                    'name' => $name,
//                    'given_name' => $c->given_name,
//                    'family_name' => $c->family_name,
//                    'last_name' => '',
//                    'first_name' => '',
//                    'contractor' => [
//                        'company_name' => $c->company_name
//                    ],
//                    'phone' => $c->primary_phone,
//                    'email' => $c->primary_email_addr,
//                ]);
//            }

        return $subs;
    }

    public
    function getAllQuickbookCompaniesAndFormattedSubs($companyName, $formattedSubs, $contractorId)
    {
//        TODO:: pull back unique contractors from the quickbooks table becuase different general contractors can have
        // TODO:: the same subs that they have worked with. I would like for this to be sorted
        // TODO:: by company name
        // TODO:: by state and city of where job is located
        // TODO:: by contractors have used in the past in this app

        $allCompanies = $this->getAllCompaniesInQuickBookContractorsByCompanyName($companyName);

        $subs = $this->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, $contractorId);

//        $qbSub = \App\QuickbooksContractor::where('given_name', '=', $c->first_name)->
//        where('last_name', '=', $c->last_name)->
//        where('contractor_id', '=', $c->id)->get()->first();
//
//        if (empty($qbSub)) {
//            $qbSub = \App\QuickbooksContractor::where('given_name', '=', $c->first_name)->
//            where('last_name', '=', $c->last_name)->get()->first();
//        }
//
//        if (empty($qbSub)) {
//            $contractorId = '';
//            $quickbooksId = '';
//        } else {
//            $contractorId = $qbSub->contractor_id;
//            $quickbooksId = $qbSub->quickbooks_id;
//        }
//


//        $subs = [];
//
//        foreach ($allCompanies as $c) {
//            $subExists = false;
//            foreach ($subs as $sub) {
//                if ($sub == $c) {
//                    $subExists = true;
//                }
//            }
//            if (!$subExists) {
//                if ($c->family_name == "NULL") {
//                    $name = $c->given_name;
//                } else if ($c->given_name == "NULL") {
//                    $name = $c->family_name;
//                } else {
//                    $name = $c->given_name . " " . $c->family_name;
//                }
//                array_push($subs, [
//                    'id' => '',
//                    'quickbooksId' => $c->quickbooks_id,
//                    'name' => $name,
//                    'given_name' => $c->given_name,
//                    'family_name' => $c->family_name,
//                    'last_name' => '',
//                    'first_name' => '',
//                    'contractor' => [
//                        'company_name' => $c->company_name
//                    ],
//                    'phone' => $c->primary_phone,
//                    'email' => $c->primary_email_addr,
//                ]);
//            }
//        }


        $subArray = [];

        if (count($formattedSubs) > count($subs)) {

            foreach ($formattedSubs as $c) {
                $subExists = false;
                foreach ($subs as $sub) {
                    if (
                        $sub['name'] == $c['name'] &&
                        $sub['contractor']['company_name'] == $c['contractor']['company_name']
                    ) {
                        $sub['id'] = $c['id'];
                        array_push($subArray, $sub);
                        $subExists = true;
                    }
                }
                if (!$subExists) {
                    array_push($subArray, $c);
                }
            }

        } else {
            foreach ($subs as $sub) {
                $subExists = false;
                foreach ($formattedSubs as $c) {
                    if (
                        $sub['name'] == $c['name'] &&
                        $sub['contractor']['company_name'] == $c['contractor']['company_name']
                    ) {
                        $sub['id'] = $c['id'];
                        array_push($subArray, $sub);
                        $subExists = true;
                    }
                }
                if (!$subExists) {
                    array_push($subArray, $c);
                }
            }
        }

        return $subArray;

    }

    public
    function getSubContractors($company_name, $generalContractorsCompanyName)
    {

//        TODO: this method does not work like it should
//        Every company in the same state is unique
//        if more than one general contractor has a cust in QB with company name and state then
//                then those contractors in QB_contractors table are the same contractor
//        I want a unique company from QB if one of those company's associate to the Auth::user()
//                general contractor then use that one for the drop down menu
//        if the QB_contractor is also a user in jemmson users table then use the jemmson user table instead
//                of the contractor in the QB_contractor table

        $subs = $this->getAllContractorsExceptTheGeneralContractor($company_name, $generalContractorsCompanyName);
        $formattedSubs = $this->subsWithPhoneNumberAndEmail($subs);

//        $qb = new Quickbook();
//        if ($qb->isContractorThatUsesQuickbooks()) {
//            return $this->getAllQuickbookCompaniesAndFormattedSubs($company_name, $formattedSubs, Auth::user()->getAuthIdentifier());
//        }

        return $formattedSubs;
    }
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];