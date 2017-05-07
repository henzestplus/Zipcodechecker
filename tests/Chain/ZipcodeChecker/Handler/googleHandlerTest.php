<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 6-5-17
 * Time: 10:46
 */

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Bounds;
use Geocoder\Model\Country;
use Stplus\Chain\ZipcodeChecker\addressPendant;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Address;
use Geocoder\Provider\GoogleMaps;

class googleHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $oMockedGoogleMapsApi;
    private $oGoogleHandler;
    private $oPendant;
    private $oAddressCollection;
    private $aExpected;

    public function setUp(){
        $this->oMockedGoogleMapsApi = $this->getMockBuilder(GoogleMaps::class)
            ->disableOriginalConstructor()
            ->setMethods(array('geocode','reverse'))
            ->getMock();

        $this->oGoogleHandler = new googleHandler();
        $ref = new \ReflectionProperty(googleHandler::class,'geocoder');
        $ref->setAccessible(true);
        $ref->setValue($this->oGoogleHandler, $this->oMockedGoogleMapsApi);
        $ref->setAccessible(false);

        $this->aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516 AC',
            'country'=>'Nederland',
            'street'=>'Regulusweg',
            'city'=>'Den Haag',
            'longitude'=>'52.074648',
            'latitude'=>'4.3419284',
            'source'=>'google'
        );

        $this->oAddressCollection = new AddressCollection(
            array(
                new Address(
                    new Coordinates('4.3419284','52.074648'),
                    new Bounds(0,0,0,0),
                    '11',
                    'Regulusweg',
                    '2516 AC',
                    'Den Haag',
                    '',
                    new AdminLevelCollection(),
                    new Country('Nederland','NL')
                )
            )
        );

        $this->oPendant = new addressPendant('11','2516 AC','Nederland');
    }
    public function testGoogleHandlerNoResults()
    {
        $this->oMockedGoogleMapsApi->method('geocode')
            ->willReturn(new AddressCollection());
        $bActual = $this->oGoogleHandler->start($this->oPendant);
        $this->assertFalse($bActual);
    }

    public function testGoogleHandlerWithStreet()
    {
        $this->oPendant->setAttribute('street','Regulusweg');


        $this->oMockedGoogleMapsApi->method('geocode')
            ->willReturn($this->oAddressCollection);

        $bActual = $this->oGoogleHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();


        $this->assertTrue($bActual);
        $this->assertEquals($this->aExpected,$aActual);
    }

    public function testGoogleHandlerWithoutStreet()
    {
        $this->oMockedGoogleMapsApi->method('geocode')
            ->willReturn( $this->oAddressCollection);
        $this->oMockedGoogleMapsApi->method('reverse')
            ->willReturn(
                $this->oAddressCollection
            );

        $bActual = $this->oGoogleHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();
        $this->assertTrue($bActual);
        $this->assertEquals($this->aExpected,$aActual);
    }
    
}
