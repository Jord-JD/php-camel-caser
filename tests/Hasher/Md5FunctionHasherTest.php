<?php

namespace JordJD\CamelCaser\Tests\Hasher;

use JordJD\CamelCaser\Hasher\Md5FunctionHasher;
use JordJD\CamelCaser\Tests\CreateFunctionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Hasher\Md5FunctionHasher
 */
class Md5FunctionHasherTest extends TestCase
{
    use CreateFunctionTrait;

    /**
     * @dataProvider functionProvider
     *
     * @param string                     $expected
     * @param ReflectionFunctionAbstract ...$functions
     *
     * @return void
     *
     * @covers ::__invoke
     */
    public function testInvoke(
        string $expected,
        ReflectionFunctionAbstract ...$functions
    ): void {
        $subject = new Md5FunctionHasher();

        $this->assertEquals($expected, $subject->__invoke(...$functions));
    }

    /**
     * @return array
     */
    public function functionProvider(): array
    {
        return [
            ['d41d8cd98f00b204e9800998ecf8427e'],
            [
                'acbd18db4cc2f85cedef654fccc4a4d8',
                $this->createFunction('foo'),
            ],
            [
                'c0c27d0b79228a06ef81a8111136edea',
                $this->createFunction('foo'),
                $this->createFunction('bar'),
                $this->createFunction('baz'),
            ],
            [
                'c0c27d0b79228a06ef81a8111136edea',
                $this->createFunction('baz'),
                $this->createFunction('bar'),
                $this->createFunction('foo'),
            ],
        ];
    }
}
