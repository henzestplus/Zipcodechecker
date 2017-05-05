<?php

class PendantTest extends PHPUnit_Framework_TestCase
{
    public function testSetAttribute()
    {
        $pendant = new \Stplus\Chain\Pendant();
        $pendant->setAttribute('test', 'string');
        $this->assertEquals('string',$pendant->getAttribute('test'));
    }

    public function testGetNotExistingAttribute()
    {
        $this->expectException('RuntimeException');
        $pendant = new \Stplus\Chain\Pendant();
        $pendant->setAttribute('test', 'string');
        $pendant->getAttribute('test2');
    }

    public function testAttributeExists()
    {
        $pendant = new \Stplus\Chain\Pendant();
        $pendant->setAttribute('test', 'string');
        $this->assertTrue($pendant->attributeExists('test'));
    }

    public function testAttributeNotExists()
    {
        $pendant = new \Stplus\Chain\Pendant();
        $pendant->setAttribute('test', 'string');
        $this->assertNotTrue($pendant->attributeExists('test2'));
    }

    public function testSetAttributeArray()
    {
        $array = array('zipcode'=>'3443HZ','streetnumber'=>7,'coordinates'=>true, 'object'=> new stdClass());
        $pendant = new \Stplus\Chain\Pendant();
        $pendant->setAttributesArray($array);
        $this->assertEquals($array, $pendant->getAttributesArray());
    }
}
