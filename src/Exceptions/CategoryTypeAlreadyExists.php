<?php

namespace BalajiDharma\LaravelCategory\Exceptions;

use InvalidArgumentException;

class CategoryTypeAlreadyExists extends InvalidArgumentException
{
    public static function create(string $machineName)
    {
        return new static("A `{$machineName}` already exists.");
    }
}
