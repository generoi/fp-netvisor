<?php

namespace Xi\Netvisor\Support;

final class Str
{
    /**
     * Multibyte-safe substring, always using UTF-8.
     */
    public static function utf8_substr(string $string, int $start, int $length): string
    {
        return \mb_substr($string, $start, $length, 'UTF-8');
    }
}

