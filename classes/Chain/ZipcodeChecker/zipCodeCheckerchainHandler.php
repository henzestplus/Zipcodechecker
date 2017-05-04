<?php

namespace Stplus\Chain\ZipcodeChecker;

use Stplus\Chain\chainHandler;
use Stplus\Chain\Pendant;

abstract class zipCodeCheckerchainHandler extends chainHandler
{
    protected function verifyPendantType(Pendant $pendant): bool
    {
        if ($pendant instanceof addressPendant) {
            if (!$pendant->attributeExists('zipcode')) {
                throw new \RuntimeException('addressPendant does not contain attribute \'zipcode\'');
            }
            if (!$pendant->attributeExists('streetnumber')) {
                throw new \RuntimeException('addressPendant does not contain attribute \'streetnumber\'');
            }
            if (!$pendant->attributeExists('country')) {
                throw new \RuntimeException('addressPendant does not contain attribute \'country\'');
            }
            return true;
        }
        throw new \RuntimeException('Pendant is not of type Stplus\Chain\ZipcodeChecker\addressPendant');
    }
}