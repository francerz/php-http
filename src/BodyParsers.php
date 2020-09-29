<?php

namespace Francerz\Http;

use Exception;
use Francerz\PowerData\Arrays;
use InvalidArgumentException;

class BodyParsers
{
    private static $parsers = array();
    private static $typeIndex = array();

    public static function register(string $parserClass)
    {
        if (!in_array(ParserInterface::class, class_implements($parserClass))) {
            throw new InvalidArgumentException(sprintf("Parser class must implement %s.", ParserInterface::class));
        }

        $filter = array_filter(static::$parsers, function($v) use ($parserClass) {
            return $v instanceof $parserClass;
        });

        if (count($filter) > 0) return;

        $parser = new $parserClass();
        static::$parsers[] = $parser;
        foreach ($parser->getSupportedTypes() as $type) {
            static::$typeIndex[$type] = $parser;
        }
    }

    public static function find(string $type) : ?ParserInterface
    {
        $lim = strpos($type, ';');
        $type = ($lim === false ? $type : substr($type, 0, $lim));
        return Arrays::valueKeyInsensitive(static::$typeIndex, $type);
    }
}