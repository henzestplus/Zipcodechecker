<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerChainHandler;

class proxyHandler extends zipCodeCheckerChainHandler
{
    public function handle(Pendant $pendant): bool
    {
        $handle = new googleHandler();
        $result = $handle->start($pendant);
        //$pendant->setAttribute('source', 'Proxy');
        return $result;
    }
}