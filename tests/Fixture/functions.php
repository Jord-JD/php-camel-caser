<?php

namespace JordJD\CamelCaser\Tests\Fixture;

use stdClass;

const FOO = 12;

/**
 * @param int $foo
 *
 * @return void
 */
function foo_default_constant_value(int $foo = FOO): void
{
}

/**
 * @param int $bar
 *
 * @return void
 */
function bar_default_value_available(int $bar = 12): void
{
}

/**
 * @param stdClass|null $baz
 *
 * @return void
 */
function baz_parameter_optional(stdClass $baz = null): void
{
}

/**
 * @param stdClass $qux
 *
 * @return void
 */
function quz_parameter_required(stdClass $qux): void
{
}

/**
 * @param null|stdClass $quu
 *
 * @return void
 */
function quu_parameter_nullable(?stdClass $quu): void
{
}
