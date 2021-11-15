<?php

use Aberdeener\Inject\Container;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Aberdeener\Inject\Container
 */
class ContainerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Container::get()->flush();
    }

    public function testAliases()
    {
        $container = Container::get();

        $container->alias(CanAddNumbers::class, AddsNumbers::class);

        $this->assertInstanceOf(AddsNumbers::class, $container->make(CanAddNumbers::class));
    }

    public function testCanBind()
    {
        $container = Container::get();

        $container->bind(Bindable::class, function () {
            return new Bindable(time() . rand(0, 999));
        });

        $binded1 = $container->make(Bindable::class);
        $binded2 = $container->make(Bindable::class);

        $this->assertSame($binded1->value, $binded2->value);
    }

    public function testCanMakeSingleton()
    {
        $container = Container::get();

        $container->singleton(Singleton::class);

        $singleton1 = $container->make(Singleton::class);
        $singleton2 = $container->make(Singleton::class);

        $this->assertSame($singleton1->value, $singleton2->value);
    }

    public function testCanBuildConstructorParams()
    {
        $container = Container::get();

        $container->singleton(Singleton::class);
        $singleton = $container->make(Singleton::class);

        $container->bind(Bindable::class, function () {
            return new Bindable(13);
        });

        $container->alias(CanAddNumbers::class, AddsNumbers::class);

        $buildMe = $container->inject(BuildMe::class);

        $this->assertInstanceOf(AddsNumbers::class, $buildMe->addsNumbers);
        $this->assertSame($singleton->value, $buildMe->singleton->value);
        $this->assertSame(13, $buildMe->bindable->value);
    }

    public function testCanBuildMethodParams()
    {
        $container = Container::get();

        $container->bind(Bindable::class, function () {
            return new Bindable(rand(0, 999));
        });

        $container->alias(CanAddNumbers::class, AddsNumbers::class);

        $buildMe = $container->inject(BuildMe::class, 'numberThings');

        $this->assertInstanceOf(AddsNumbers::class, $buildMe->canAddNumbers);
        $this->assertInstanceOf(AddsNumbers::class, $buildMe->addsNumbersAgain);
    }
}

interface CanAddNumbers
{
    public function add(int $num, int $num2): int;
}

class AddsNumbers implements CanAddNumbers
{
    public function add(int $num, int $num2): int
    {
        return $num + $num2;
    }
}

class Bindable
{
    public function __construct(
        public int $value
    ) {}
}

class Singleton
{
    public int $value;

    public function __construct() {
        $this->value = time() . rand(0, 999);
    }
}

class BuildMe
{
    public function __construct(
        public CanAddNumbers $addsNumbers,
        public Singleton $singleton,
        public Bindable $bindable
    ) {}

    public CanAddNumbers $canAddNumbers;
    public AddsNumbers $addsNumbersAgain;

    public function numberThings(
        CanAddNumbers $canAddNumbers,
        AddsNumbers $addsNumbersAgain
    ): BuildMe {
        $this->canAddNumbers = $canAddNumbers;
        $this->addsNumbersAgain = $addsNumbersAgain;

        return $this;
    }
}
