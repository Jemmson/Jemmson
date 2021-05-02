<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use oasis\names\specification\ubl\schema\xsd\CommonBasicComponents_2\BalanceBroughtForwardIndicator;

class AssessorController extends Controller
{
    //

    protected $baseUrl = 'https://preview.mcassessor.maricopa.gov/';

    public function getLocation($location)
    {

        $address = str_replace('+', ' ', $location);
        if (strpos($address, 'east')) {
            $address = str_replace_first('east', 'e', $address);
        } else if (strpos($address, 'south')) {
            $address = str_replace_first('south', 's', $address);
        } else if (strpos($address, 'west')) {
            $address = str_replace_first('west', 'w', $address);
        } else if (strpos($address, 'north')) {
            $address = str_replace_first('north', 'n', $address);
        }

        $location = '/search/' . str_replace('+', ' ', $address);

        $response = $this->getAssessorData($location);

        $result = json_decode($response);

        $totalFound = (int)$result->rp->TOTAL;

        if ($totalFound === 0) {
            return response()->json([
                'error' => 'There was no assessor information available'
            ]);
        } else if ($totalFound === 1) {
            $parcel = 'parcel/' . $result->rp->Results[0]->APN;
            return $this->getAssessorData($parcel);
        } else {
            return $result->rp->Results;
        }
    }

    public function getParcel($apn)
    {
        $parcel = 'parcel/' . $apn;
        return $this->getAssessorData($parcel);
    }

    private function getAssessorData($location)
    {
        $client = new Client();
        $url = $this->baseUrl . $location;
        try {
            return $client->request('GET', $url, [
                'headers' => [
                    'AUTHORIZATION' => '1034e75e-89c6-11e8-8a04-00155da2c015'
                ]
            ])
                ->getBody()
                ->getContents();
        } catch (RequestException $e) {
            Log::debug($e->getMessage());
        }
    }

}