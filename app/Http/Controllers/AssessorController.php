<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AssessorController extends Controller
{
    //

    protected $baseUrl = 'https://api.mcassessor.maricopa.gov/';

    public function getLocation($location)
    {

        $location = '/api/search/property/' . $location;

        $response = $this->getAssessorData($location);

        dd($response);

//        $phpArray = json_decode($response);
//
//        if (count($phpArray['Results']) > 1) {
//            return response()->json([
//                'results' => count($phpArray['Results'])
//            ], 200);
//        }

    }

    private function getAssessorData($location)
    {
        $client = new Client();
        $url = $this->baseUrl . $location;
        try {
            return $client->request('GET', $url, [
                'headers' => [
                    'X-MC-AUTH' => '1034e75e-89c6-11e8-8a04-00155da2c015'
                ]
            ])
                ->getBody()
                ->getContents();
        } catch (RequestException $e) {
            Log::debug($e->getMessage());
        }
    }

}
