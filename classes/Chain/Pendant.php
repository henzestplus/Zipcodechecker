<?php

namespace Stplus\Chain;

class Pendant
{
    private $data = array();

    public function setAttribute(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getAttribute(string $name)
    {
        if ($this->attributeExists($name)) {
            return $this->data[$name];
        } else {
            throw new \RuntimeException("No value exists with that name: $name");
        }
    }

    public function attributeExists(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function getAttributesArray(): array
    {
        return $this->data;
    }

    public function setAttributesArray(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }
}