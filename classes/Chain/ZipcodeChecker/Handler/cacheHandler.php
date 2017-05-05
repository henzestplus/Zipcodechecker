<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerchainHandler;

class cacheHandler extends zipcodeCheckerChainHandler
{
    private $memcacheD;

    public function __construct(\Memcached $memcacheD)
    {
        $this->memcacheD = $memcacheD;
    }

    protected function handle(Pendant $pendant): bool
    {
        return $this->addCachedResultsIfExistsTo($pendant);
    }

    private function addCachedResultsIfExistsTo(Pendant $pendant): bool
    {
        $key = $pendant->getAttribute('zipcode') . $pendant->getAttribute('streetnumber');
        $cached = $this->memcacheD->get($key);
        if ($cached) {
            $pendant->setAttributesArray($cached);
            if (!$pendant->attributeExists('original_source')) {
                $pendant->setAttribute('original_source', $pendant->getAttribute('source'));
                $pendant->setAttribute('source', 'cache');
            }
            return true;
        }
        return false;
    }
}