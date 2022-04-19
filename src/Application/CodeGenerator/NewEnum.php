<?php

namespace App\Application\CodeGenerator;

use Elao\Enum\EnumInterface;

final class NewEnum implements \Stringable
{
    private EnumInterface $enum;

    public function __construct(EnumInterface $enum)
    {
        $this->enum = $enum;
    }

    public function __toString(): string
    {
        $reflectionClass = new \ReflectionClass($this->enum);
        $constantName = array_flip($reflectionClass->getConstants())[$this->enum->getValue()];

        return sprintf('%s::%s()', $reflectionClass->getShortName(), $constantName);
    }
}
