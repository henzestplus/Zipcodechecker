<?php
/**
 * Created by PhpStorm.
 * User: henze
 * Date: 5-5-17
 * Time: 21:33
 */

namespace Stplus\Chain\ZipcodeChecker;
use Stplus\Chain\Pendant;


class zipCodeCheckerChainHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInjectingAddressPendant()
    {
        $oHandler = $this->getMockBuilder(zipCodeCheckerChainHandler::class)
            ->getMock();

        $oHandler
            ->method('handle')
            ->willReturn(true);

        $bActual = $oHandler->start(new addressPendant('11','2516AC','Nederland'));
        $this->assertTrue($bActual);
    }

    public function testInjectingWrongPendantType()
    {
        $this->expectException('RuntimeException');
        $oHandler = $this->getMockBuilder(zipCodeCheckerChainHandler::class)
            ->getMock();

        $oHandler
            ->method('handle')
            ->willReturn(true);

        $oHandler->start(new Pendant());
    }
}
