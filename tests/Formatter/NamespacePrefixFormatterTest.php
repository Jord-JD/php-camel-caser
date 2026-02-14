<?php

namespace JordJD\CamelCaser\Tests\Formatter;

use JordJD\CamelCaser\Formatter\FunctionFormatterInterface;
use JordJD\CamelCaser\Formatter\NamespacePrefixFormatter;
use JordJD\CamelCaser\Tests\CreateFunctionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Formatter\NamespacePrefixFormatter
 */
class NamespacePrefixFormatterTest extends TestCase
{
    use CreateFunctionTrait;

    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        /* @noinspection PhpParamsInspection */
        $this->assertInstanceOf(
            NamespacePrefixFormatter::class,
            new NamespacePrefixFormatter(
                $this->createMock(FunctionFormatterInterface::class)
            )
        );
    }

    /**
     * @dataProvider functionProvider
     *
     * @param ReflectionFunctionAbstract $function
     * @param string                     $expected
     *
     * @return void
     *
     * @covers ::__invoke
     */
    public function testInvoke(
        ReflectionFunctionAbstract $function,
        string $expected
    ): void {
        /** @var FunctionFormatterInterface|MockObject $formatter */
        $formatter = $this->createMock(FunctionFormatterInterface::class);
        $subject = new NamespacePrefixFormatter($formatter);

        $formatter
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::isInstanceOf(ReflectionFunctionAbstract::class))
            ->willReturnCallback(
                function (ReflectionFunctionAbstract $function): string {
                    return strtoupper($function->getShortName());
                }
            );

        $this->assertEquals($expected, $subject->__invoke($function));
    }

    /**
     * @return array
     */
    public function functionProvider(): array
    {
        return [
            [
                $this->createFunction('foo'),
                '\FOO',
            ],
            [
                $this->createFunction('Foo\Bar\baz'),
                '\Foo\Bar\BAZ',
            ],
        ];
    }
}
