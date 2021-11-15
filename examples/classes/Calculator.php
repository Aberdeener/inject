<?php

namespace Aberdeener\Inject\Examples;

class Calculator
{
    public function __construct(
        private CanAddNumbers $addsNumbers
    ) {
    }

    public function addNumbers(int $num, int $num2)
    {
        return $this->addsNumbers->add($num, $num2);
    }
}
