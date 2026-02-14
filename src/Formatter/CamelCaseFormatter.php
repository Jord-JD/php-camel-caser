<?php

namespace JordJD\CamelCaser\Formatter;

use ReflectionFunctionAbstract;

class CamelCaseFormatter implements FunctionFormatterInterface
{
    use CamelCaseTrait;

    /**
     * Format the given function reflection into a string.
     *
     * @param ReflectionFunctionAbstract $function
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract $function): string
    {
        return static::camelCase(
            // Use the name without namespace.
            $function->getShortName()
        );
    }
}
