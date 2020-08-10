<?php

namespace Francerz\Http;

class Ports
{
    public static function forScheme(string $scheme) : array
    {
        switch (strtolower($scheme)) {
            case 'http': return [80];
            case 'https': return [443];
        }
        return [];
    }
}