<?php

namespace Stplus\Chain\ZipcodeChecker;

use Stplus\Chain\chainHandler;
use Stplus\Chain\Pendant;

abstract class zipCodeCheckerChainHandler extends chainHandler
{
    protected function verifyPendantType(Pendant $pendant): bool
    {
        if ($pendant instanceof addressPendant) {
            return true;
        }
        throw new \RuntimeException('Pendant is not of type Stplus\Chain\ZipcodeChecker\addressPendant');
    }
}