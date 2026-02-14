<?php

namespace JordJD\CamelCaser\Tests\Integration;

use JordJD\CamelCaser\Formatter\CamelCaseTrait;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionParameter;
use Throwable;
use const JordJD\CamelCaser\EXCLUDE_DISABLED_FUNCTIONS;

/**
 * Assert that internal functions get a camel cased alias, when applicable.
 * This test assumes that the autoloader will be used.
 */
class InternalFunctionAliasTest extends TestCase
{
    use CamelCaseTrait;

    /**
     * @dataProvider functionProvider
     *
     * @param string $original
     * @param string $expected
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFunctionAlias(string $original, string $expected): void
    {
        $this->assertTrue(
            function_exists($original),
            'The original function must always exist.'
        );

        $this->assertTrue(
            function_exists($expected),
            'The alias function must always exist.'
        );

        $originalReflection = new ReflectionFunction($original);
        $this->assertTrue(
            $originalReflection->isInternal(),
            'Original function must be an internal function.'
        );

        $aliasReflection = new ReflectionFunction($expected);
        $this->assertTrue(
            $aliasReflection->isUserDefined(),
            'Alias function must be user defined.'
        );
    }

    /**
     * @return array
     */
    public function functionProvider(): array
    {
        return array_reduce(
            get_defined_functions(EXCLUDE_DISABLED_FUNCTIONS)['internal'] ?? [],
            function (array $carry, string $function): array {
                $alias = self::camelCase($function);

                if ($alias !== $function
                    && !empty($alias)
                    && $this->functionHasValidSignature($function)
                    // In some cases, internal functions have internal aliases,
                    // although not officially documented.
                    && !$this->isInternal($alias)
                ) {
                    $carry[] = [$function, $alias];
                }

                return $carry;
            },
            []
        );
    }

    /**
     * Determine whether the supplied function is an existing internal function.
     *
     * @param string $function
     *
     * @return bool
     */
    private function isInternal(string $function): bool
    {
        try {
            $reflection = new ReflectionFunction($function);

            return $reflection->isInternal();
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * Verify that the signature of the given function does not hold multiple
     * parameters with the same name.
     *
     * @see https://bugs.php.net/bug.php?id=76918
     *
     * @param string $function
     *
     * @return bool
     */
    private function functionHasValidSignature(string $function): bool
    {
        $reflection = new ReflectionFunction($function);
        $parameters = array_map(
            function (ReflectionParameter $parameter): string {
                return $parameter->getName();
            },
            $reflection->getParameters()
        );

        return $parameters === array_unique($parameters);
    }
}
