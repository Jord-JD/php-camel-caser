<?php

namespace JordJD\CamelCaser\Alias;

use Iterator;

interface AliasIteratorInterface extends Iterator
{
    /**
     * Get the current alias.
     *
     * @return AliasInterface
     */
    public function current(): AliasInterface;
}
