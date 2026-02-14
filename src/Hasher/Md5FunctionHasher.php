<?php

namespace JordJD\CamelCaser\Hasher;

use ReflectionFunctionAbstract;

class Md5FunctionHasher implements FunctionHasherInterface
{
    /**
     * Hash the given functions into a repeatable hash string.
     *
     * @param ReflectionFunctionAbstract ...$functions
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract ...$functions): string
    {
        $names = array_map(
            function (ReflectionFunctionAbstract $function): string {
                return $function->getName();
            },
            $functions
        );

        sort($names);

        return md5(
            implode(':', $names)
        );
    }
}
