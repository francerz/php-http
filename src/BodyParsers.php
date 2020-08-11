<?php

namespace Francerz\Http;

use Francerz\PowerData\Arrays;

class BodyParsers
{
    private static array $parsers = array();
    private static array $typeIndex = array();

    public static function register(ParserInterface $parser)
    {
        $parserClass = get_class($parser);
        $filter = array_filter(static::$parsers, function($v) use ($parserClass) {
            return $v instanceof $parserClass;
        });

        if (count($filter) > 0) return;

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