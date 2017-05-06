<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 6-5-17
 * Time: 8:32
 */

namespace Stplus\Chain\ZipcodeChecker\Handler;


use Stplus\Chain\ZipcodeChecker\addressPendant;

class postcodeApiHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testApiResponseFailure()
    {
        $apiHandler = $this
            ->getMockBuilder(postcodeApiHandler::class)
            ->setMethods(array('getApiResponse'))
            ->getMock();
        $response = new \stdClass();
        $response->_embedded = array();
        $apiHandler->method('getApiResponse')->willReturn($response);
        $pendant = new addressPendant('11','2516 AC','Nederland');

        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516 AC',
            'country'=>'Nederland',
        );

        $bActual = $apiHandler->start($pendant);
        $aActual = $pendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
        $this->assertFalse($bActual);
    }

    public function testApiResponseSuccess()
    {
        $apiHandler = $this
            ->getMockBuilder(postcodeApiHandler::class)
            ->setMethods(array('getApiResponse'))
            ->getMock();
        //response from mocked api
        $address = new \stdClass();
        $address->street = 'Regulusweg';
        $city = new \stdClass();
        $city->label = "Den Haag";
        $address->city = $city;
        $geo = new \stdClass();
        $geo->center = new \stdClass();
        $geo->center->wgs84 = new \stdClass();
        $geo->center->wgs84->coordinates = array('4.3419284','52.074648');
        $address->geo = $geo;
        $response = new \stdClass();
        $response->_embedded = new \stdClass();
        $response->_embedded->addresses = array(
            $address
        );

        $apiHandler->method('getApiResponse')->willReturn($response);
        $pendant = new addressPendant('11','2516 AC','Nederland');

        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516 AC',
            'country'=>'Nederland',
            'streetname'=>'Regulusweg',
            'city'=>'Den Haag',
            'longitude'=>'4.3419284',
            'latitude'=>'52.074648',
            'source'=>'PostcodeAPI.nu'
        );

        $bActual = $apiHandler->start($pendant);
        $aActual = $pendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
        $this->assertTrue($bActual);
    }
}
