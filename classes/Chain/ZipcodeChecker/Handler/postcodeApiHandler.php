<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Http\Adapter\Guzzle6 as Guzzle;
use GuzzleHttp as GuzzleHTTP;
use FH\PostcodeAPI as PostcodeApi;

use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\addressPendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerChainHandler;

class postcodeApiHandler extends zipCodeCheckerChainHandler
{
    private $apiClient;

    public function handle(Pendant $pendant): bool
    {
        $response = $this->getApiResponse($pendant);
        if($this->verifyApiResponse($response)) {
            $this->addApiResponseTo($response, $pendant);
            return true;
        }
        return false;
    }



    private function getApiClient(): PostcodeApi\Client
    {
        if (!empty($this->apiClient)) {
            return $this->apiClient;
        }
        $apiKey = 'duWWwqMfHA8HOmBrsnjUg1scdLlCiQ2D4sSSJpf6';
        $this->apiClient = new PostcodeAPI\Client(
            new Guzzle\Client(
                new GuzzleHTTP\Client([
                    'headers' => [
                        'X-Api-Key' => $apiKey
                    ]
                ])
            )
        );
        return $this->apiClient;
    }

    private function formatZipcode(string $zipcode):string
    {
        return str_replace(' ', '', $zipcode);
    }

    private final function getApiResponse(addressPendant $pendant):\stdClass
    {
        return $this->getApiClient()->getAddresses(
            $this->formatZipcode($pendant->getAttribute("zipcode")),
            $pendant->getAttribute("streetnumber"));
    }

    private final function addApiResponseTo(\stdClass $response, addressPendant $pendant)
    {
        $address = reset($response->_embedded->addresses);
        $pendant->setAttribute('streetname', $address->street);
        $pendant->setAttribute('city', $address->city->label);
        $pendant->setAttribute('longitude', $address->geo->center->wgs84->coordinates[0]);
        $pendant->setAttribute('latitude', $address->geo->center->wgs84->coordinates[1]);
        $pendant->setAttribute('source', 'PostcodeAPI.nu');
    }

    private function verifyApiResponse(\stdClass $response):bool
    {
        return ($response && !empty($response->_embedded->addresses));
    }

}