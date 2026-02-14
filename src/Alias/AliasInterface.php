<?php

namespace JordJD\CamelCaser\Alias;

use ReflectionFunctionAbstract;

interface AliasInterface
{
    /**
     * Get the original function name.
     *
     * @return string
     */
    public function getOriginal(): string;

    /**
     * Get the function alias.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Get a reflection of the original function.
     *
     * @return ReflectionFunctionAbstract
     */
    public function getReflection(): ReflectionFunctionAbstract;
}
