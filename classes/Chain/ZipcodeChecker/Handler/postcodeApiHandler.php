<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Http\Adapter\Guzzle6 as Guzzle;
use GuzzleHttp as GuzzleHTTP;
use FH\PostcodeAPI as PostcodeApi;

use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerChainHandler;

class postcodeApiHandler extends zipCodeCheckerChainHandler
{
    private $apiClient;

    public function handle(Pendant $pendant): bool
    {
        return $this->addApiResultsTo($pendant);
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

    private final function addApiResultsTo(Pendant $pendant):bool
    {
        $result = $this->getApiClient()->getAddresses($pendant->getAttribute("zipcode"),
            $pendant->getAttribute("streetnumber"));
        if ($result && $result->_embedded && !empty($result->_embedded->addresses)) {
            $address = reset($result->_embedded->addresses);
            $pendant->setAttribute('streetname', $address->street);
            $pendant->setAttribute('city', $address->city->label);
            $pendant->setAttribute('longitude', $address->geo->center->wgs84->coordinates[0]);
            $pendant->setAttribute('latitude', $address->geo->center->wgs84->coordinates[1]);
            $pendant->setAttribute('source', 'PostcodeAPI.nu');
            return true;
        }
        return false;
    }
}