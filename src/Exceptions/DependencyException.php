<?php
namespace Laradic\Console\Exceptions;
use Exception;
class DependencyException extends Exception
{
    public static function missing($name)
    {
        return new static("Missing dependency [{$name}]");
    }
    public static function invalidVersion($expected, $current = null)
    {

        return new static("Wrong version. Expected: {$expected}");
    }
}
