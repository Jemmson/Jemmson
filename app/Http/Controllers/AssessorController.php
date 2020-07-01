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

        $result = json_decode($response);

        $totalFound = (int)$result->TotalFound;

        if ($totalFound === 0) {
            return response()->json([
                'error' => 'There was no assessor information available'
            ]);
        } else if ($totalFound === 1) {
            return $this->getAssessorData($result->Results[0]->APN->link);
        } else {
            return $result->Results;
        }

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
