<?php

namespace JordJD\CamelCaser\Alias\Renderer;

use JordJD\CamelCaser\Alias\AliasInterface;

interface AliasRendererInterface
{
    /**
     * Render code for the given aliases.
     *
     * @param AliasInterface ...$aliases
     *
     * @return string
     */
    public function __invoke(AliasInterface ...$aliases): string;
}
