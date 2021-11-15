<?php

use Aberdeener\Inject\Container;

// get Container instance.
$container = Container::get();

// tell the Container that the RandomNumber class should be a singleton .
// this means each time we make() a RandomNumber, it will give us back the same instance and not make a new one.
$container->singleton(RandomNumber::class);

// get the instance of RandomNumber.
// since it's the first time it is being taken out of Container, it will be constructed
// and then returned
$randomNumber1 = $container->make(RandomNumber::class);

// pause execution for 1 second
sleep(1);

// get the RandomNumber instance from the Container again
// this time it will return the exact same instance with the same random number value.
// if we did not make this a singleton, a new instance would be created, 
// which would have a different random number value since we paused for 1 second
$randomNumber2 = $container->make(RandomNumber::class);

echo $randomNumber1->number === $randomNumber2->number; // true
