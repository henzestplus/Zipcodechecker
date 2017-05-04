<?php

namespace Stplus\Chain;

abstract class chainHandler
{

    private $nextHandler;

    private final function getNextHandler(): chainHandler
    {
        return $this->nextHandler;
    }

    private final function hasNextHandler(): bool
    {
        return !empty($this->nextHandler);
    }

    public final function setNextHandler(chainHandler $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }

    public final function start(Pendant $pendant): bool
    {
        if (!$this->verifyPendantType($pendant)) {
            throw new \UnexpectedValueException('Incorrect pendant type given');
        }
        $handled = $this->handle($pendant);
        if (!$handled && $this->hasNextHandler()) {
            return $this->getNextHandler()->start($pendant);
        }
        return $handled;
    }

    protected abstract function verifyPendantType(Pendant $pendant): bool;

    protected abstract function handle(Pendant $pendant): bool;

}