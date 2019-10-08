<?php

namespace App\Http\Controllers;

use App\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('contractors.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Contractor $contractor
     * @return \Illuminate\Http\Response
     */
    public function show(Contractor $contractor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Contractor $contractor
     * @return \Illuminate\Http\Response
     */
    public function edit(Contractor $contractor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contractor $contractor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contractor $contractor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Contractor $contractor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contractor $contractor)
    {
        //
    }

    public function hideStripeModal()
    {

        if (Auth::user()->usertype == 'contractor') {
            $contractor = Auth::user()->contractor()->get()->first();
            $contractor->hide_stripe_modal = true;
            try {
                $contractor->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }
        }


    }


    public function getContractors($company_name)
    {

        $contractor = new Contractor();
        $generalContractorsCompanyName = $contractor->getGeneralContractorsCompanyName();

        return $contractor->getSubContractors($company_name, $generalContractorsCompanyName);

    }

}
