<?php

namespace Aberdeener\Inject\Examples;

class AddsNumbers implements CanAddNumbers
{

    public function add(int $num, int $num2)
    {
        return $num + $num2;
    }

}
