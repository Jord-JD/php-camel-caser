<?php

namespace JordJD\CamelCaser\Tests\Alias\Finder;

use JordJD\CamelCaser\Alias\AliasIteratorInterface;
use JordJD\CamelCaser\Alias\Finder\AliasFinder;
use JordJD\CamelCaser\Formatter\FunctionFormatterInterface;
use JordJD\CamelCaser\Tests\CreateFunctionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Alias\Finder\AliasFinder
 */
class AliasFinderTest extends TestCase
{
    use CreateFunctionTrait;

    /**
     * @dataProvider functionProvider
     *
     * @param int                        $expected
     * @param callable                   $aliasCallback
     * @param ReflectionFunctionAbstract ...$functions
     *
     * @return void
     *
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke(
        int $expected,
        callable $aliasCallback,
        ReflectionFunctionAbstract ...$functions
    ): void {
        /** @var FunctionFormatterInterface|MockObject $originalFormatter */
        $originalFormatter = $this->createMock(FunctionFormatterInterface::class);

        /** @var FunctionFormatterInterface|MockObject $aliasFormatter */
        $aliasFormatter = $this->createMock(FunctionFormatterInterface::class);

        $subject = new AliasFinder($originalFormatter, $aliasFormatter);

        $originalFormatter
            ->expects(self::exactly(count($functions)))
            ->method('__invoke')
            ->with(self::isInstanceOf(ReflectionFunctionAbstract::class))
            ->willReturnCallback(
                function (ReflectionFunctionAbstract $function): string {
                    return $function->getName();
                }
            );

        $aliasFormatter
            ->expects(self::exactly(count($functions)))
            ->method('__invoke')
            ->with(self::isInstanceOf(ReflectionFunctionAbstract::class))
            ->willReturnCallback($aliasCallback);

        $result = $subject->__invoke(...$functions);

        $this->assertInstanceOf(AliasIteratorInterface::class, $result);
        $this->assertCount($expected, iterator_to_array($result));
    }

    /**
     * @return array
     */
    public function functionProvider(): array
    {
        return [
            [
                0,
                function (): void {
                    // Nothing to do.
                },
            ],
            [
                0,
                function (ReflectionFunctionAbstract $function): string {
                    return $function->getName();
                },
                $this->createFunction('in_array'),
            ],
            [
                0,
                function (): string {
                    return '';
                },
                $this->createFunction('in_array'),
            ],
            [
                1,
                function (ReflectionFunctionAbstract $function): string {
                    return md5($function->getName());
                },
                $this->createFunction('in_array'),
            ],
            [
                1,
                function (ReflectionFunctionAbstract $function): string {
                    return md5($function->getName());
                },
                $this->createFunction('in_array'),
                $this->createFunction('in_array'),
                $this->createFunction('in_array'),
            ],
            [
                2,
                function (ReflectionFunctionAbstract $function): string {
                    return md5($function->getName());
                },
                $this->createFunction('in_array'),
                $this->createFunction('array_key_exists'),
                $this->createFunction('in_array'),
            ],
        ];
    }
}
