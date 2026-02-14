<?php

namespace JordJD\CamelCaser\Alias;

use ReflectionFunctionAbstract;

class Alias implements AliasInterface
{
    /** @var string */
    private $original;

    /** @var string */
    private $alias;

    /** @var ReflectionFunctionAbstract */
    private $reflection;

    /**
     * Constructor.
     *
     * @param string                     $original
     * @param string                     $alias
     * @param ReflectionFunctionAbstract $reflection
     */
    public function __construct(
        string $original,
        string $alias,
        ReflectionFunctionAbstract $reflection
    ) {
        $this->original = $original;
        $this->alias = $alias;
        $this->reflection = $reflection;
    }

    /**
     * Get the original function name.
     *
     * @return string
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * Get the function alias.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Get a reflection of the original function.
     *
     * @return ReflectionFunctionAbstract
     */
    public function getReflection(): ReflectionFunctionAbstract
    {
        return $this->reflection;
    }
}
