<?php

use Aberdeener\Inject\Container;
use Aberdeener\Inject\Examples\CanAddNumbers;
use Aberdeener\Inject\Examples\AddsNumbers;
use Aberdeener\Inject\Examples\Calculator;

// get Container instance.
$container = Container::get();

// alias the CanAddNumbers interface to the AddsNumbers class.
// now when we have a typehint for CanAddNumbers or AddsNumbers, it will be given an instance of AddsNumbers.
$container->alias(
    CanAddNumbers::class,
    AddsNumbers::class
);

// create our calculator class instance.
// the CanAddNumbers constructor parameter will be injected with an instance of AddsNumbers
$calculator = $container->make(Calculator::class);

echo $calculator->addNumbers(1, 2); // 3
