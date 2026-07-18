<?php

namespace JordJD\CamelCaser;

use JordJD\CamelCaser\Alias\Finder\AliasFinder;
use JordJD\CamelCaser\Alias\Renderer\AliasRenderer;
use JordJD\CamelCaser\Formatter\CamelCaseFormatter;
use JordJD\CamelCaser\Formatter\FunctionFormatter;
use JordJD\CamelCaser\Formatter\NamespacePrefixFormatter;
use JordJD\CamelCaser\Hasher\Md5FunctionHasher;
use ReflectionFunction;
use RuntimeException;
use Throwable;

const INCLUDE_DISABLED = false;
const EXCLUDE_DISABLED_FUNCTIONS = true;

$internalFunctions = array_map(
    function (string $function): ReflectionFunction {
        return new ReflectionFunction($function);
    },
    get_defined_functions(EXCLUDE_DISABLED_FUNCTIONS)['internal'] ?? []
);

$hasher = new Md5FunctionHasher();
$cacheDirectory = sys_get_temp_dir().'/jord-jd-php-camel-caser-'.sha1(__DIR__);
$rendererHash = md5_file(__DIR__.'/src/Alias/Renderer/AliasRenderer.php');
$storage = sprintf(
    '%s/aliases.%s.%s.php',
    $cacheDirectory,
    $hasher(...$internalFunctions),
    $rendererHash
);

if (!file_exists($storage)) {
    if (!is_dir($cacheDirectory) && !@mkdir($cacheDirectory, 0700, true) && !is_dir($cacheDirectory)) {
        throw new RuntimeException('Failed to create the internal-function alias cache directory.');
    }

    $finder = new AliasFinder(
        new NamespacePrefixFormatter(new FunctionFormatter()),
        new NamespacePrefixFormatter(new CamelCaseFormatter())
    );
    $renderer = new AliasRenderer();

    try {
        $render = $renderer(...$finder(...$internalFunctions));
    } catch (Throwable $exception) {
        throw new RuntimeException('Failed to generate aliases for internal functions.', 0, $exception);
    }

    $temporaryPath = tempnam($cacheDirectory, 'aliases-');

    if ($temporaryPath === false || file_put_contents($temporaryPath, $render, LOCK_EX) === false) {
        throw new RuntimeException('Failed to write aliases for internal functions.');
    }

    @chmod($temporaryPath, 0600);

    if (!rename($temporaryPath, $storage)) {
        @unlink($temporaryPath);

        if (!file_exists($storage)) {
            throw new RuntimeException('Failed to install aliases for internal functions.');
        }
    }
}

try {
    require_once $storage;
} catch (Throwable $exception) {
    @unlink($storage);

    throw new RuntimeException('Failed to register aliases for internal functions.', 0, $exception);
}
