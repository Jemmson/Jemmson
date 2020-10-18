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
            $bestGuess = self::bestGuessOption($result->Results, $location);
            return $this->getAssessorData($bestGuess->APN->link);

        }

    }

    public function bestGuessOption($results, $location)
    {
        try {
            $situsAddress = [];
            $locationArray = explode('+', $location);
            $houseNumberArray = explode('/', $locationArray[0]);
            $houseNumber = $houseNumberArray[count($houseNumberArray) - 1];
            $locationArray[0] = $houseNumber;
            for ($i = 0; $i < count($results); $i++){
                $addressArray = explode(' ', $results[$i]->SitusAddress);
                if ($results[$i]->SitusAddress === '2345 S ALMA SCHOOL RD MESA, AZ 85210') {
                    $found = true;
                }
                $matchedAddress = [];
                foreach ($addressArray as $address) {
                    if (strlen($address) > 0) {
                        foreach ($locationArray as $item) {
                            if (strtolower($item) === strtolower($address)) {
                                array_push($matchedAddress, $item);
                                break;
                            } else if (
                                $item[0] &&
                                strtolower($item[0]) === strtolower($address[0])
                            ) {
                                array_push($matchedAddress, $item);
                                break;
                            }
                        }
                    }
                }

                if (count($matchedAddress) === count($locationArray)) {
                    array_push($situsAddress, $results[$i]);
                }
            }
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
        }



        if (count($situsAddress) === 1) {
            return $situsAddress[0];
        } else {
            return $situsAddress[0];
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
