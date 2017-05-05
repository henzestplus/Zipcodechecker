<?php
class addressPendantTest extends PHPUnit_Framework_TestCase
{
    public function testMinimalAttributeExists()
    {
        $addressPendant = new \Stplus\Chain\ZipcodeChecker\addressPendant('11', '2516AC', 'Nederland');

        $aExpected = array(
            'streetnumber'=>'11',
            'zipcode'=>'2516AC',
            'country'=>'Nederland'
        );
        $this->assertEquals($aExpected,$addressPendant->getAttributesArray());
    }
}
