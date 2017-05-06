<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 6-5-17
 * Time: 8:32
 */

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Stplus\Chain\ZipcodeChecker\addressPendant;
use FH\PostcodeAPI\Client;

class postcodeApiHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $oMockedPostcodeApiClient;
    private $oApiHandler;
    private $oPendant;

    public function setUp(){
        $this->oMockedPostcodeApiClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getAddresses'))
            ->getMock();

        $this->oApiHandler = new postcodeApiHandler();
        $ref = new \ReflectionProperty(get_class($this->oApiHandler),'apiClient');
        $ref->setAccessible(true);
        $ref->setValue($this->oApiHandler, $this->oMockedPostcodeApiClient);
        $ref->setAccessible(false);

        $this->oPendant = new addressPendant('11','2516 AC','Nederland');
    }
    public function testApiResponseFailure()
    {
        $this->oMockedPostcodeApiClient->method('getAddresses')
            ->willReturn(new \stdClass());

        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516 AC',
            'country'=>'Nederland',
        );

        $bActual = $this->oApiHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
        $this->assertFalse($bActual);
    }

    public function testApiResponseSuccess()
    {
        //response from mocked api
        $oAddress = new \stdClass();
        $oAddress->street = 'Regulusweg';
        $oCity = new \stdClass();
        $oCity->label = "Den Haag";
        $oAddress->city = $oCity;
        $oGeo = new \stdClass();
        $oGeo->center = new \stdClass();
        $oGeo->center->wgs84 = new \stdClass();
        $oGeo->center->wgs84->coordinates = array('4.3419284','52.074648');
        $oAddress->geo = $oGeo;
        $oResponse = new \stdClass();
        $oResponse->_embedded = new \stdClass();
        $oResponse->_embedded->addresses = array(
            $oAddress
        );
        $this->oMockedPostcodeApiClient->method('getAddresses')
            ->willReturn($oResponse);

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

        $bActual = $this->oApiHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
        $this->assertTrue($bActual);
    }
}
