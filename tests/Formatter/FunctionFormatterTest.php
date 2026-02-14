<?php

namespace JordJD\CamelCaser\Tests\Formatter;

use JordJD\CamelCaser\Formatter\FunctionFormatter;
use JordJD\CamelCaser\Tests\CreateFunctionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Formatter\FunctionFormatter
 */
class FunctionFormatterTest extends TestCase
{
    use CreateFunctionTrait;

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
        $subject = new FunctionFormatter();

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
                'foo',
            ],
            [
                $this->createFunction('foo_bar'),
                'foo_bar',
            ],
            [
                $this->createFunction('fooBar'),
                'fooBar',
            ],
        ];
    }
}
