<?php

namespace Aberdeener\Inject\Examples;

class RandomNumber
{
    public int $number;

    public function __construct() {
        $this->number = time() . rand(0, 999);
    }
}
