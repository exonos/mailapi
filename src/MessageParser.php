<?php

namespace Exonos\Mailapi;

class MessageParser
{

    public static function substituteValues(string $message, array $substitution): string
    {
        collect($substitution)->each(function ($sub) use (&$message) {
            $pattern = '{$' . $sub['var'] . '}';
            $message = str_replace($pattern, $sub['value'], $message);

        });
        return $message;
    }
}