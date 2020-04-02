<?php

namespace App\Http\Controllers;

use App\StripeExpress;
use App\StripeUploadedFiles;
use Illuminate\Http\Request;
use Stripe\File;

class StripeExpressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

//    https://stripe.com/docs/file-upload
    public function supportingDocs(Request $request)
    {

        $apiKey = StripeExpress::getAPIKey($request->accountId);

        \Stripe\Stripe::setApiKey($apiKey);
        $document = $request->files->get('file');

        $fp = fopen($document->getRealPath(), 'r');

        $createdDocument = \Stripe\File::create([
            'file' => $fp,
            'purpose' => 'dispute_evidence'
        ]);

        $uploadedFile = new StripeUploadedFiles();
        $uploadedFile->create($createdDocument, $request->accountId);

        echo $createdDocument;


//        // Set your secret key. Remember to switch to your live secret key in production!
//        // See your keys here: https://dashboard.stripe.com/account/apikeys
//        \Stripe\Stripe::setApiKey('sk_test_ebg7SjOI3rsZkeV5SZsUkOon');
//
//        $file = $request->files->get('file');
//
//        \Stripe\File::create([
//            'file' => ‌‌$file,
//            'purpose' => 'dispute_evidence'
//        ]);

//        dd($request);
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
     * @param \App\StripeExpress $stripeExpress
     * @return \Illuminate\Http\Response
     */
    public function show(StripeExpress $stripeExpress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\StripeExpress $stripeExpress
     * @return \Illuminate\Http\Response
     */
    public function edit(StripeExpress $stripeExpress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\StripeExpress $stripeExpress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StripeExpress $stripeExpress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\StripeExpress $stripeExpress
     * @return \Illuminate\Http\Response
     */
    public function destroy(StripeExpress $stripeExpress)
    {
        //
    }
}
