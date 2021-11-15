<?php

namespace Aberdeener\Inject;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionMethod;

final class Container
{
    private const CONSTRUCTOR = '__construct';

    private static Container $instance;

    private array $instances = [];
    private array $singletons = [];
    private array $aliases = [];
    private string $lastInjectMethod = '';

    public static function get(): Container
    {
        return self::$instance ??= new Container();
    }

    public function flush(): void
    {
        self::$instance = new Container();
    }

    public function make(string $class)
    {
        $class = $this->getAliasedClass($class);

        if ($this->isSingleton($class)) {
            return $this->instances[$class] ??= new $class(...$this->buildParams($class));
        }

        return new $class(...$this->buildParams($class));
    }

    public function inject(string $class, string $method = self::CONSTRUCTOR)
    {
        $this->lastInjectMethod = $method;

        if ($method === self::CONSTRUCTOR) {
            return new $class(...$this->buildParams($class, $method));
        }

        return $this->make($class)->{$method}(...$this->buildParams($class, $method));
    }

    public function bind(string $class, Closure $callback)
    {
        $this->singleton($class);

        $instance = $callback();

        if (!($instance instanceof $class)) {
            $instanceClass = $instance::class;
            throw new Exception("Cannot bind instance of {$instanceClass} to {$class}.\nInstances returned by callback in bind() method must match class bound to.");
        }

        $this->instances[$class] = $instance;
    }

    public function alias(string $interface, string $alias)
    {
        if (!interface_exists($interface)) {
            throw new Exception("The interface {$interface} does not exist.");
        }

        $this->aliases[$interface] = $alias;
    }

    private function getAliasedClass(string $classOrInterface)
    {
        if (isset($this->aliases[$classOrInterface])) {
            return $this->aliases[$classOrInterface];
        }

        if (interface_exists($classOrInterface)) {
            throw new Exception("No alias setup for the {$classOrInterface} interface. Cannot initialize an interface.");
        }

        return $classOrInterface;
    }

    public function singleton(string $class)
    {
        $this->singletons[] = $class;
    }

    private function isSingleton(string $class): bool
    {
        return in_array($class, $this->singletons);
    }

    private function buildParams(string $class, string $method = self::CONSTRUCTOR): array
    {
        $params = [];

        $reflectionClass = new ReflectionClass($class);
        if (!$reflectionClass->hasMethod($method)) {
            return $params;
        }

        $reflectionMethod = new ReflectionMethod($class, $method);
        $reflectionParams = $reflectionMethod->getParameters();

        foreach ($reflectionParams as $param) {
            $type = $param->getType();

            if (class_exists($type->getName()) || interface_exists($type->getName())) {
                $params[] = $this->make($type->getName());
                continue;
            }

            $exceptionMessage = "{$type->getName()} is not a valid class or interface. Cannot make instance of primative type.";

            if ($this->lastInjectMethod != $method) {
                $exceptionMessage .= "\nDid you forget to bind an instance of {$class} to use instead of the {$class}::{$method} method?";
            }

            throw new Exception($exceptionMessage);
        }

        return $params;
    }
}
