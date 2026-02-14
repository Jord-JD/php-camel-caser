<?php

namespace JordJD\CamelCaser\Formatter;

use ReflectionFunctionAbstract;

interface FunctionFormatterInterface
{
    /**
     * Format the given function reflection into a string.
     *
     * @param ReflectionFunctionAbstract $function
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract $function): string;
}
