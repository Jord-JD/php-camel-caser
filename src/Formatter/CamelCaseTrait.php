<?php

namespace JordJD\CamelCaser\Formatter;

trait CamelCaseTrait
{
    /**
     * Convert the given string into camel case.
     *
     * @param string $subject
     *
     * @return string
     */
    public static function camelCase(string $subject): string
    {
        return trim(
            lcfirst(
                str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace(
                            ['-', '_'],
                            ' ',
                            $subject
                        )
                    )
                )
            )
        );
    }
}
