<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 5-5-17
 * Time: 22:06
 */

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Stplus\Chain\ZipcodeChecker\addressPendant;

class cacheHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $cacheHandler;
    private $oPendant;

    public function setUp()
    {
        $this->cacheHandler = $this->getMockBuilder(cacheHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getFromCache'
            ))
            ->getMock();

        $this->oPendant = new addressPendant('11','2516AC','Nederland');
    }

    public function testCacheHandlerSuccess()
    {
        $aExpected = array(
            'street'=>'Regulusweg',
            'streetnumber'=>'11',
            'zipcode'=>'2516AC',
            'country'=>'Nederland',
            'source'=>'cache',
            'original_source'=>'unittest'
        );
        $this->cacheHandler->method('getFromCache')
            ->willReturnCallback(function($key){
                $aReturn = array(
                    'street'=>'Regulusweg',
                    'streetnumber'=>'11',
                    'zipcode'=>'2516AC',
                    'country'=>'Nederland',
                    'source'=>'unittest'
                );
                if($key===$aReturn['zipcode'].$aReturn['streetnumber']) {
                    return $aReturn;
                }
                return array();
            });

        $this->cacheHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
    }

    public function testCacheHandlerFailure()
    {
        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516AC',
            'country'=>'Nederland',
        );
        $this->cacheHandler->method('getFromCache')
            ->willReturn(array());
        $this->cacheHandler->start($this->oPendant);
        $aActual = $this->oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
    }
}
