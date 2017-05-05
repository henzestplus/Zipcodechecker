<?php

namespace Stplus\Chain;

class PendantTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAttribute()
    {
        $pendant = new Pendant();
        $pendant->setAttribute('test', 'string');
        $sExpected = 'string';
        $sActual = $pendant->getAttribute('test');
        $this->assertEquals($sExpected,$sActual);
    }

    public function testGetNotExistingAttribute()
    {
        $this->expectException('RuntimeException');
        $pendant = new Pendant();
        $pendant->setAttribute('test', 'string');
        $pendant->getAttribute('test2');
    }

    public function testAttributeExists()
    {
        $pendant = new Pendant();
        $pendant->setAttribute('test', 'string');
        $bActual = $pendant->attributeExists('test');
        $this->assertTrue($bActual);
    }

    public function testAttributeNotExists()
    {
        $pendant = new Pendant();
        $pendant->setAttribute('test', 'string');
        $bActual = $pendant->attributeExists('test2');
        $this->assertNotTrue($bActual);
    }

    public function testSetAttributeArray()
    {
        $aExpected = array('zipcode'=>'3443HZ',
            'streetnumber'=>7,
            'coordinates'=>true,
            'object'=> new \stdClass()
        );
        $pendant = new Pendant();
        $pendant->setAttributesArray($aExpected);
        $aActual = $pendant->getAttributesArray();
        $this->assertEquals($aExpected, $aActual);
    }
}
