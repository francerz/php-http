<?php
namespace Francerz\Http;

use Francerz\Http\Base\ResponseBase;
use Francerz\Http\Traits\MessageTrait;
use Francerz\Http\Traits\ResponseTrait;

class Response extends ResponseBase
{
    use MessageTrait;
    use ResponseTrait;

    public function __construct()
    {
        parent::__construct();
        $this->body = new StringStream();
    }

    protected function importHeaders($headers_string)
    {
        $headers = explode("\r\n", $headers_string);

        for ($i = 2; $i < count($headers); $i++) {
            $h = $headers[$i];
            if (empty($h)) continue;
            if (stripos($h, 'HTTP') === 0) continue;
            list($header, $h_content) = explode(':', $h);
            $this->headers[$header] = preg_split('/,\\s*/', trim($h_content));
        }
    }

    public static function fromCURL($curl, string $response_body = '') : Response
    {
        $response = new static();
        $response->code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        $header_size  = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header_space = trim(substr($response_body, 0, $header_size));
        $response->importHeaders($header_space);

        $content      = substr($response_body, $header_size);
        $response->body = new StringStream($content);

        return $response;
    }
}