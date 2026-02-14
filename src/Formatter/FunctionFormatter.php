<?php

namespace JordJD\CamelCaser\Formatter;

use ReflectionFunctionAbstract;

class FunctionFormatter implements FunctionFormatterInterface
{
    /**
     * Format the given function reflection into a string.
     *
     * @param ReflectionFunctionAbstract $function
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract $function): string
    {
        return $function->getShortName();
    }
}
