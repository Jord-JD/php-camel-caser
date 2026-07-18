<?php

namespace JordJD\CamelCaser\Alias\Renderer;

use JordJD\CamelCaser\Alias\AliasInterface;
use JordJD\CamelCaser\Formatter\CamelCaseTrait;
use ReflectionParameter;
use ReflectionType;
use RuntimeException;
use Throwable;

class AliasRenderer implements AliasRendererInterface
{
    use CamelCaseTrait;

    /**
     * Render code for the given aliases.
     *
     * @param AliasInterface ...$aliases
     *
     * @return string
     */
    public function __invoke(AliasInterface ...$aliases): string
    {
        $code = '<?php'.PHP_EOL;

        foreach ($this->groupNamespaces(...$aliases) as $namespace => $group) {
            $code .= sprintf('namespace %s {', $namespace).PHP_EOL;

            foreach ($group as $alias) {
                try {
                    $code .= $this->renderFunction($alias);
                } catch (Throwable $exception) {
                    // Some internal functions expose signatures that cannot be
                    // represented consistently across supported PHP versions.
                    continue;
                }
            }

            $code .= '}'.PHP_EOL;
        }

        return $code;
    }

    /**
     * Group the given list of alias by their namespace.
     *
     * @param AliasInterface ...$aliases
     *
     * @return AliasInterface[][]
     */
    private function groupNamespaces(AliasInterface ...$aliases): array
    {
        return array_reduce(
            $aliases,
            function (array $carry, AliasInterface $alias): array {
                $carry[$alias->getReflection()->getNamespaceName()][] = $alias;

                return $carry;
            },
            []
        );
    }

    /**
     * Render a function for the given alias.
     *
     * @param AliasInterface $alias
     *
     * @return string
     */
    private function renderFunction(AliasInterface $alias): string
    {
        return sprintf(
            <<<'FUNCTION'
    if (!function_exists('\%2$s')) {
        /**%3$s
         * @see %1$s
         * %5$s
         */
        function %4$s%2$s(%6$s)
        {
%7$s
        }
    }

FUNCTION
            ,
            $alias->getOriginal(),
            preg_replace(
                '/.*?([^\\\\]+)$/',
                '$1',
                $alias->getAlias()
            ),
            $this->renderParameterDoc(
                '         * ',
                ...$alias
                ->getReflection()
                ->getParameters()
            ),
            $alias->getReflection()->returnsReference() ? '&' : '',
            $this->renderReturnTypeDoc(
                $alias->getReflection()->getReturnType()
            ),
            $this->renderParameters(
                ...$alias->getReflection()->getParameters()
            ),
            $this->renderForwarder(
                $alias->getOriginal(),
                ...$alias->getReflection()->getParameters()
            )
        ).PHP_EOL;
    }

    /**
     * Render a list of parameters for the doc block.
     *
     * @param string              $prefix
     * @param ReflectionParameter ...$parameters
     *
     * @return string
     */
    private function renderParameterDoc(
        string $prefix,
        ReflectionParameter ...$parameters
    ): string {
        return array_reduce(
            $parameters,
            function (
                string $carry,
                ReflectionParameter $parameter
            ) use ($prefix): string {
                return $carry.PHP_EOL.$prefix.sprintf(
                    '@param %s %s$%s',
                    $this->renderTypeDoc($parameter->getType()),
                    $parameter->isVariadic() ? '...' : '',
                    static::camelCase(
                        $parameter->getName()
                    )
                );
            },
            ''
        );
    }

    /**
     * Render the type.
     *
     * @param null|ReflectionType $type
     *
     * @return string
     */
    private function renderTypeDoc(?ReflectionType $type): string
    {
        $types = [];

        if ($type === null) {
            $types[] = 'mixed';
        } else {
            if ($type->allowsNull()) {
                $types[] = 'null';
            }

            $types[] = (string) $type;
        }

        return implode('|', $types);
    }

    /**
     * Render a return type for the doc block.
     *
     * @param null|ReflectionType $returnType
     *
     * @return string
     */
    private function renderReturnTypeDoc(
        ?ReflectionType $returnType
    ): string {
        return sprintf(
            '@return %s',
            $this->renderTypeDoc($returnType)
        );
    }

    private function renderParameters(ReflectionParameter ...$parameters): string
    {
        $rendered = [];
        $names = [];

        foreach ($parameters as $parameter) {
            $name = static::camelCase($parameter->getName());

            if (isset($names[$name])) {
                throw new RuntimeException('Cannot render an alias with duplicate parameter names.');
            }

            $names[$name] = true;
            $rendered[] = sprintf(
                '%s%s$%s%s',
                $parameter->isPassedByReference() ? '&' : '',
                $parameter->isVariadic() ? '...' : '',
                $name,
                $this->renderDefaultValue($parameter)
            );
        }

        return implode(', ', $rendered);
    }

    private function renderArguments(array $parameters): string
    {
        return implode(
            ', ',
            array_map(
                function (ReflectionParameter $parameter): string {
                    return ($parameter->isVariadic() ? '...' : '').'$'.static::camelCase($parameter->getName());
                },
                $parameters
            )
        );
    }

    private function renderForwarder(string $original, ReflectionParameter ...$parameters): string
    {
        if (count($parameters) === 0) {
            return '            return '.$original.'();';
        }

        $lines = ['            switch (func_num_args()) {'];

        for ($argumentCount = 0; $argumentCount < count($parameters); ++$argumentCount) {
            $lines[] = sprintf(
                '                case %d: return %s(%s);',
                $argumentCount,
                $original,
                $this->renderArguments(array_slice($parameters, 0, $argumentCount))
            );
        }

        $lines[] = '                default: return '.$original.'('.$this->renderArguments($parameters).');';
        $lines[] = '            }';

        return implode(PHP_EOL, $lines);
    }

    private function renderDefaultValue(ReflectionParameter $parameter): string
    {
        if ($parameter->isVariadic() || !$parameter->isOptional()) {
            return '';
        }

        try {
            if ($parameter->isDefaultValueAvailable()) {
                return ' = '.var_export($parameter->getDefaultValue(), true);
            }
        } catch (Throwable $exception) {
            // Fall back below for internal signatures without exposed defaults.
        }

        return ' = null';
    }

}
