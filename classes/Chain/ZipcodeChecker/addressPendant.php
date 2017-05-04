<?php

namespace Stplus\Chain\ZipcodeChecker;

use Stplus\Chain\Pendant;

class addressPendant extends Pendant
{
    public function __construct(string $streetnumber, string $zipcode, string $country, string $street = null)
    {
        $this->setAttribute('streetnumber', $streetnumber);
        $this->setAttribute('zipcode', $zipcode);
        $this->setAttribute('country', $country);
        if (!empty($street)) {
            $this->setAttribute('street', $street);
        }
    }
}