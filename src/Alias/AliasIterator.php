<?php

namespace JordJD\CamelCaser\Alias;

use ArrayIterator;
use IteratorIterator;

class AliasIterator extends IteratorIterator implements AliasIteratorInterface
{
    /**
     * Constructor.
     *
     * @param AliasInterface ...$aliases
     */
    public function __construct(AliasInterface ...$aliases)
    {
        parent::__construct(
            new ArrayIterator($aliases)
        );
    }

    /**
     * Get the current alias.
     *
     * @return AliasInterface
     */
    public function current(): AliasInterface
    {
        return parent::current();
    }
}
