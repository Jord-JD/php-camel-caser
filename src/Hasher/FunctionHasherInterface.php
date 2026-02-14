<?php

namespace JordJD\CamelCaser\Hasher;

use ReflectionFunctionAbstract;

interface FunctionHasherInterface
{
    /**
     * Hash the given functions into a repeatable hash string.
     *
     * @param ReflectionFunctionAbstract ...$functions
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract ...$functions): string;
}
