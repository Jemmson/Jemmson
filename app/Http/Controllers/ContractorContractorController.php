<?php

namespace App\Http\Controllers;

use App\ContractorContractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorContractorController extends Controller
{
    //

    public function getSubs(Request $request)
    {
        return ContractorContractor::getSubsNameAndId(Auth::user()->getAuthIdentifier());
    }

}
