<?php

namespace JordJD\CamelCaser\Formatter;

use ReflectionFunctionAbstract;

class NamespacePrefixFormatter implements FunctionFormatterInterface
{
    /** @var FunctionFormatterInterface */
    private $formatter;

    /**
     * Constructor.
     *
     * @param FunctionFormatterInterface $formatter
     */
    public function __construct(FunctionFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Format the given function reflection into a string.
     *
     * @param ReflectionFunctionAbstract $function
     *
     * @return string
     */
    public function __invoke(ReflectionFunctionAbstract $function): string
    {
        $formatted = $this->formatter->__invoke($function);

        return $function->inNamespace()
            ? sprintf(
                '\\%s\\%s',
                $function->getNamespaceName(),
                $formatted
            )
            : sprintf('\\%s', $formatted);
    }
}
