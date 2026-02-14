<?php

namespace DivineOmega\CamelCaser\Alias\Renderer;

use DivineOmega\CamelCaser\Alias\AliasInterface;
use DivineOmega\CamelCaser\Formatter\CamelCaseTrait;
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
                } catch (RuntimeException $exception) {
                    // Skip the erroneous function.
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
            return %1$s(...func_get_args());
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
                str_repeat(' ', 8),
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

    /**
     * Render a list of parameters.
     *
     * @param string              $indent
     * @param ReflectionParameter ...$parameters
     *
     * @throws RuntimeException When duplicate parameters are encountered.
     *
     * @return string
     */
    private function renderParameters(
        string $indent,
        ReflectionParameter ...$parameters
    ): string {
        $list = array_reduce(
            $parameters,
            function (
                array $carry,
                ReflectionParameter $parameter
            ) use ($indent): array {
                $name = static::camelCase(
                    $parameter->getName()
                );

                $value = $this->getDefaultParameterValue($parameter);

                // @see https://bugs.php.net/bug.php?id=76918
                if (array_key_exists($name, $carry)) {
                    throw new RuntimeException(
                        sprintf(
                            'Encountered duplicate parameter "%s" for %s.',
                            $name,
                            $parameter->getDeclaringFunction()->getName()
                        )
                    );
                }

                $carry[$name] = $indent.'    '.trim(
                    sprintf(
                        '%1$s %2$s$%3$s%4$s',
                        $parameter->hasType()
                            ? (string) $parameter->getType()
                            : '',
                        $parameter->isVariadic() ? '...' : '',
                        $name,
                        empty($value) ? '' : sprintf(' = %s', $value)
                    )
                );

                return $carry;
            },
            []
        );

        $list = implode(','.PHP_EOL, $list);

        if (strlen($list) > 0) {
            $list = PHP_EOL.$list.PHP_EOL.$indent;
        }

        return $list;
    }

    /**
     * Get the default value of the given parameter.
     *
     * @param ReflectionParameter $parameter
     *
     * @return string
     */
    private function getDefaultParameterValue(
        ReflectionParameter $parameter
    ): string {
        // Variadic parameter cannot have a default value.
        // This needs to be checked before isOptional.
        if ($parameter->isVariadic()) {
            return '';
        }

        // Cannot determine default value for internal functions.
        if ($parameter->getDeclaringFunction()->isInternal()) {
            return '';
        }

        try {
            if ($parameter->isDefaultValueConstant()) {
                return $parameter->getDefaultValueConstantName();
            }

            if ($parameter->isDefaultValueAvailable()) {
                return json_encode($parameter->getDefaultValue());
            }
        } catch (Throwable $exception) {
            return '';
        }

        // @codeCoverageIgnoreStart
        // No scenario known for this state.
        return '';
        // @codeCoverageIgnoreEnd
    }
}
