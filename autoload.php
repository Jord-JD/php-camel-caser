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
use SplFileObject;
use Throwable;

const INCLUDE_DISABLED = false;
const EXCLUDE_DISABLED_FUNCTIONS = true;

$internalFunctions = array_map(
    function (string $function) : ReflectionFunction {
        return new ReflectionFunction($function);
    },
    get_defined_functions(EXCLUDE_DISABLED_FUNCTIONS)['internal'] ?? []
);

$hasher = new Md5FunctionHasher();
$storage = sprintf(
    __DIR__.'/aliases.%s.php',
    $hasher(...$internalFunctions)
);

if (!file_exists($storage)) {
    $finder = new AliasFinder(
        new NamespacePrefixFormatter(
            new FunctionFormatter()
        ),
        new NamespacePrefixFormatter(
            new CamelCaseFormatter()
        )
    );

    $aliases = $finder(...$internalFunctions);
    $renderer = new AliasRenderer();

    try {
        $render = $renderer(...$aliases);
    } catch (Throwable $exception) {
        $render = null;
    }

    if (is_string($render)) {
        $file = new SplFileObject($storage, 'w+');
        $file->fwrite($render);
    }
}

try {
    require_once $storage;
} catch (Throwable $exception) {
    unlink($storage);

    throw new RuntimeException(
        'Failed to register aliases for internal functions!',
        0,
        $exception
    );
}
