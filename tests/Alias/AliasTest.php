<?php

namespace JordJD\CamelCaser\Tests\Alias;

use JordJD\CamelCaser\Alias\Alias;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

/**
 * @coversDefaultClass \JordJD\CamelCaser\Alias\Alias
 */
class AliasTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getAlias
     * @covers ::getOriginal
     * @covers ::getReflection
     */
    public function testModel(): void
    {
        $subject = new Alias(
            'in_array',
            'inArray',
            new ReflectionFunction('in_array')
        );

        $this->assertInstanceOf(Alias::class, $subject);
        $this->assertEquals('in_array', $subject->getOriginal());
        $this->assertEquals('inArray', $subject->getAlias());
        $this->assertInstanceOf(
            \ReflectionFunctionAbstract::class,
            $subject->getReflection()
        );
    }
}
