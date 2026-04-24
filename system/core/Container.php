<?php

declare(strict_types=1);

namespace Ziro\System;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    protected array $bindings = [];
    protected array $instances = [];

    public function bind(string $abstract, callable $factory)
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory)
    {
        $this->instances[$abstract] = $factory($this);
    }

    public function make(string $abstract)
    {
        // If already singleton
        if (isset($this->instances[$abstract])) return $this->instances[$abstract];

        // If manually bound
        if (isset($this->bindings[$abstract])) return $this->bindings[$abstract]($this);

        // Auto resolve
        return $this->resolve($abstract);
    }

    public function resolve(string $class)
    {
        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) throw new \Exception("Cannot instatiate $class");

        $constructor = $reflection->getConstructor();
        if (!$constructor) return new $class;

        $dependencies = array_map(
            fn(ReflectionParameter $param) =>
            $this->resolveDependency($param),
            $constructor->getParameters()
        );
        return $reflection->newInstanceArgs($dependencies);
    }

    public function resolveDependency(ReflectionParameter $param)
    {
        $type = $param->getType();
        if (!$type) throw new \Exception("Unresolved dependency");

        return $this->make($type->getName());
    }
}
