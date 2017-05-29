<?php

namespace Stplus\Chain\ZipcodeChecker\Handler;

use Stplus\Chain\Pendant;
use Stplus\Chain\ZipcodeChecker\zipCodeCheckerChainHandler;

class cacheHandler extends zipCodeCheckerChainHandler
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

    protected function getFromCache(string $key)
    {
        return $this->memcacheD->get($key);
    }

    private function addCachedResultsIfExistsTo(Pendant $pendant): bool
    {
        $key = $pendant->getAttribute('zipcode') . $pendant->getAttribute('streetnumber');
        $cached = $this->getFromCache($key);
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