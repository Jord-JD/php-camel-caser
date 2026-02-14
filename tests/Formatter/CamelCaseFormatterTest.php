<?php

namespace JordJD\CamelCaser\Tests\Formatter;

use JordJD\CamelCaser\Formatter\CamelCaseFormatter;
use JordJD\CamelCaser\Tests\CreateFunctionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Formatter\CamelCaseFormatter
 */
class CamelCaseFormatterTest extends TestCase
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
        $subject = new CamelCaseFormatter();

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
                'fooBar',
            ],
            [
                $this->createFunction('fooBar'),
                'fooBar',
            ],
        ];
    }
}
