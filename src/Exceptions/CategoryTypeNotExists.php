<?php

namespace BalajiDharma\LaravelCategory\Exceptions;

use InvalidArgumentException;

class CategoryTypeNotExists extends InvalidArgumentException
{
    public static function create(string $machineName)
    {
        return new static("A `{$machineName}` category type not exists.");
    }
}
