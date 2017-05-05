<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 5-5-17
 * Time: 20:23
 */

namespace Stplus\Chain;


class chainHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testVerifyPendantTypeSuccessful()
    {
        $chainHandler = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $chainHandler->method('verifyPendantType')
            ->willReturn(true);

        $bExpected = $chainHandler->start(new Pendant());
        $this->assertFalse($bExpected);
    }

    public function testVerifyPendantTypeFailure()
    {
        $chainHandler = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $chainHandler->method('verifyPendantType')
            ->willReturn(false);

        $this->expectException('UnexpectedValueException');
        $chainHandler->start(new Pendant());
    }

    public function testPassingObjectByReference()
    {
        $pendant = new Pendant();
        $oChainHandler = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $oChainHandler->method('verifyPendantType')
            ->willReturn(true);
        
        $oChainHandler->method('handle')
            ->willReturnCallback(function(Pendant $pendant){
                $pendant->setAttribute('test','response');
                return true;
            });
        $oChainHandler->start($pendant);

        $sExpected = 'response';
        $aActual = $pendant->getAttribute('test');
        $this->assertEquals($sExpected,$aActual);
    }

    public function testChainOfResponsibility()
    {
        //first handler
        $oChainHandler = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $oChainHandler->method('verifyPendantType')
            ->willReturn(true);

        $oChainHandler->method('handle')
            ->willReturn(false);

        //second handler
        $oChainHandler2 = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $oChainHandler2->method('verifyPendantType')
            ->willReturn(true);

        $oChainHandler2->method('handle')
            ->willReturn(true); //this handler is the one that gives our response!
        $oChainHandler->setNextHandler($oChainHandler2);

        //third handler should never be reached.
        $oChainHandler3 = $this->getMockBuilder(chainHandler::class)
            ->getMock();

        $oChainHandler3->method('verifyPendantType')
            ->willReturn(true);

        $oChainHandler3->method('handle')
            ->willReturn(false);
        $oChainHandler2->setNextHandler($oChainHandler3);

        $oChainHandler3->expects($this->never())->method('start');

        $aActual = $oChainHandler->start(new Pendant());
        $this->assertEquals(true, $aActual);
    }
}
