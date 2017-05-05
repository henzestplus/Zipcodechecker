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
    public function testCacheHandlerSuccess()
    {
        $cacheHandler = $this->getMockBuilder(cacheHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getFromCache'
            ))
            ->getMock();

        $aExpected = array(
            'street'=>'Regulusweg',
            'streetnumber'=>'11',
            'zipcode'=>'2516AC',
            'country'=>'Nederland',
            'source'=>'cache',
            'original_source'=>'unittest'
        );
        $cacheHandler->method('getFromCache')
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
        $oPendant = new addressPendant('11','2516AC','Nederland');
        $cacheHandler->start($oPendant);
        $aActual = $oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
    }

    public function testCacheHandlerFailure()
    {
        $cacheHandler = $this->getMockBuilder(cacheHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getFromCache'
            ))
            ->getMock();

        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516AC',
            'country'=>'Nederland',
        );
        $cacheHandler->method('getFromCache')
            ->willReturn(array());
        $oPendant = new addressPendant('11','2516AC','Nederland');
        $cacheHandler->start($oPendant);
        $aActual = $oPendant->getAttributesArray();
        $this->assertEquals($aExpected,$aActual);
    }
}
