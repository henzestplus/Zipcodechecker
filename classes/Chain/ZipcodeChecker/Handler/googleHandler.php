<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Geocoder\Model\AddressCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Address;
use Geocoder\Provider\GoogleMaps;

use Ivory\HttpAdapter\CurlHttpAdapter;
use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerchainHandler;

class googleHandler extends zipCodeCheckerchainHandler
{
    private $geocoder;

    public function handle(Pendant $pendant): bool
    {
        $result = $this->fetchAddressDetailsFromGoogle($pendant);
        if ($this->verifyGoogleResult($pendant, $result)) {
            $this->addAddressDetailsTo($pendant, $result);
            return true;
        }
        return false;
    }

    private function fetchAddressDetailsFromGoogle(Pendant $pendant): Address
    {
        $googleResult = new Address();
        $address = $this->formatAddressStringForGoogle($pendant);
        if ($pendant->attributeExists('street')) {
            $googleResults = $this->getAddressCollectionFromGoogle($address);
            if ($googleResults->count() > 0) {
                return $googleResults->first();
            }
        } else {
            $coordinates = $this->getCoordinatesFromGoogle($address);
            if ($coordinates->getLatitude()>0) {
                $googleResults = $this->getAddressCollectionFromGoogleWithCoordinates($coordinates);
                if ($googleResults->count() > 0) {
                    $pendant->setAttribute('street', $googleResults->first()->getStreetName());
                    $pendant->setAttribute('city', $googleResults->first()->getLocality());
                    return $this->fetchAddressDetailsFromGoogle($pendant);
                }
            }
        }
        return $googleResult;
    }

    private function formatAddressStringForGoogle(Pendant $pendant): string
    {
        $address = $this->formatZipcode($pendant->getAttribute('zipcode'));
        if ($pendant->attributeExists('street')) {
            $address = $pendant->getAttribute('street') . ' ' . $pendant->getAttribute('streetnumber');
        }
        if ($pendant->attributeExists('city')) {
            $address .= ' '.$pendant->getAttribute('city');
        }
        $address .= ', '.$pendant->getAttribute('country');
        return $address;
    }

    private function formatZipcode(string $zipcode):string
    {
        $zipcode = str_replace(' ', '', $zipcode);
        return substr($zipcode, 0, 4) . ' ' . substr($zipcode, 4);
    }

    private function getAddressCollectionFromGoogle(string $address): AddressCollection
    {
        try {
            return $this->getGeoCoder()->geocode($address);
        } catch (\Exception $e) {
            return new AddressCollection();
        }
    }

    private final function getGeoCoder(): GoogleMaps
    {
        if (empty($this->geocoder)) {
            $adapter = new CurlHttpAdapter();
            $this->geocoder = new GoogleMaps($adapter);
            $this->geocoder->setLocale('nl-NL');
            $this->geocoder->limit(1);
        }
        return $this->geocoder;
    }

    private function getCoordinatesFromGoogle(string $address): Coordinates
    {
        $resultsFromGoogle = $this->getAddressCollectionFromGoogle($address);
        if ($resultsFromGoogle->count() > 0) {
            return $resultsFromGoogle->first()->getCoordinates();
        }
        return new Coordinates(0,0);
    }

    private function getAddressCollectionFromGoogleWithCoordinates(Coordinates $coordinates): AddressCollection
    {
        $resultsFromGoogle = $this->getGeoCoder()->reverse($coordinates->getLatitude(), $coordinates->getLongitude());
        if ($resultsFromGoogle->count() > 0) {
            return $resultsFromGoogle;
        }
        return new AddressCollection();
    }

    private function verifyGoogleResult(Pendant $pendant, Address $googleResult): bool
    {
        if ($this->formatZipcode($pendant->getAttribute('zipcode')) !== $googleResult->getPostalCode()) {
            return false;
        }

        if ($pendant->getAttribute('streetnumber') !== $googleResult->getStreetNumber()) {
            return false;
        }
        return true;
    }

    private function addAddressDetailsTo(Pendant $pendant, Address $googleResult)
    {
        $pendant->setAttribute('street', $googleResult->getStreetName());
        $pendant->setAttribute('city', $googleResult->getLocality());
        $pendant->setAttribute('longitude', $googleResult->getLongitude());
        $pendant->setAttribute('latitude', $googleResult->getLatitude());
        $pendant->setAttribute('source', 'google');
    }
}