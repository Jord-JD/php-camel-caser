<?php

namespace DivineOmega\CamelCaser\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionFunctionAbstract;

trait CreateFunctionTrait
{
    /**
     * @param string $name
     *
     * @return ReflectionFunctionAbstract
     */
    private function createFunction(string $name): ReflectionFunctionAbstract
    {
        /** @var ReflectionFunctionAbstract|MockObject $function */
        $function = $this->createMock(ReflectionFunctionAbstract::class);

        $function
            ->expects(TestCase::any())
            ->method('getName')
            ->willReturn($name);

        $function
            ->expects(TestCase::any())
            ->method('getShortName')
            ->willReturn(
                preg_replace(
                    '/.*?([^\\\\]+)$/',
                    '$1',
                    $name
                )
            );

        $function
            ->expects(TestCase::any())
            ->method('inNamespace')
            ->willReturn(
                strpos($name, '\\') !== false
            );

        $function
            ->expects(TestCase::any())
            ->method('getNamespaceName')
            ->willReturn(
                trim(
                    preg_replace(
                        '/(.*?)([^\\\\]+)$/',
                        '$1',
                        $name
                    ),
                    '\\'
                )
            );

        return $function;
    }

    /**
     * The host test case (PHPUnit\Framework\TestCase) already provides createMock().
     * This trait intentionally relies on that implementation.
     */
}
