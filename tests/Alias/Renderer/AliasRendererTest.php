<?php

namespace JordJD\CamelCaser\Tests\Alias\Renderer;

use JordJD\CamelCaser\Alias\AliasInterface;
use JordJD\CamelCaser\Alias\Renderer\AliasRenderer;
use JordJD\CamelCaser\Formatter\CamelCaseTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Alias\Renderer\AliasRenderer
 */
class AliasRendererTest extends TestCase
{
    use CamelCaseTrait;

    /**
     * @dataProvider aliasProvider
     *
     * @param AliasInterface ...$aliases
     *
     * @return void
     *
     * @covers ::__invoke
     * @covers ::groupNamespaces
     * @covers ::renderFunction
     * @covers ::renderParameterDoc
     * @covers ::renderTypeDoc
     * @covers ::renderReturnTypeDoc
     * @covers ::renderParameters
     * @covers ::getDefaultParameterValue
     */
    public function testInvoke(AliasInterface ...$aliases): void
    {
        $subject = new AliasRenderer();

        $this->assertStringStartsWith(
            '<?php',
            $subject->__invoke(...$aliases)
        );
    }

    /**
     * @param string                          $original
     * @param string                          $alias
     * @param ReflectionFunctionAbstract|null $reflection
     *
     * @return AliasInterface
     */
    public function createAlias(
        string $original,
        string $alias,
        ReflectionFunctionAbstract $reflection = null
    ): AliasInterface {
        /** @var AliasInterface|MockObject $mock */
        $mock = $this->createMock(AliasInterface::class);

        $mock
            ->expects(self::any())
            ->method('getOriginal')
            ->willReturn($original);

        $mock
            ->expects(self::any())
            ->method('getAlias')
            ->willReturn($alias);

        $mock
            ->expects(self::any())
            ->method('getReflection')
            ->willReturn(
                $reflection ?? new ReflectionFunction($original)
            );

        return $mock;
    }

    /**
     * @return array
     */
    public function aliasProvider(): array
    {
        return [
            [],
            array_map(
                function (string $original): AliasInterface {
                    return $this->createAlias(
                        $original,
                        $this->camelCase($original)
                    );
                },
                get_defined_functions(true)['internal'] ?? []
            ),
            array_map(
                function (string $original): AliasInterface {
                    return $this->createAlias(
                        $original,
                        $this->camelCase($original)
                    );
                },
                [
                    '\JordJD\CamelCaser\Tests\Fixture\foo_default_constant_value',
                    '\JordJD\CamelCaser\Tests\Fixture\bar_default_value_available',
                    '\JordJD\CamelCaser\Tests\Fixture\baz_parameter_optional',
                    '\JordJD\CamelCaser\Tests\Fixture\quz_parameter_required',
                    '\JordJD\CamelCaser\Tests\Fixture\quu_parameter_nullable',
                ]
            ),
        ];
    }
}
