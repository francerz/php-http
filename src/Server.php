<?php

namespace Francerz\Http;

use Francerz\Http\Utils\ServerInterface;
use Psr\Http\Message\ResponseInterface;

class Server implements ServerInterface
{
    public function emitResponse(ResponseInterface $response)
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        echo $response->getBody();
    }
}