<?php

use Aberdeener\Inject\Examples\Person;
use Aberdeener\Inject\Container;

// get Container instance.
$container = Container::get();

// tell the Container to make the Person class a singleton, but use a specific instance.
$container->bind(Person::class, function () {
    return new Person('tadhg boyle');
});

// get the instance of Person from the Container.
// since we used bind(), it will return the Person we just created ^.
$person = $container->make(Person::class);

echo $person->name; // tadhg boyle