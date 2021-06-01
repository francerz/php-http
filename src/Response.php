<?php
namespace Francerz\Http;

use Psr\Http\Message\ResponseInterface;

class Response extends AbstractMessage implements ResponseInterface
{
    protected $code;
    protected $reasonPhrase;

    public function __construct()
    {
        parent::__construct();
        $this->body = new StringStream();
    }

    public function getStatusCode() : int
    {
        return $this->code;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;

        $new->code = $code;
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @deprecated v0.3.0
     *
     * @param string $headers_string
     * @return void
     */
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

    /**
     * @deprecated v0.3.0
     *
     * @param \CurlHandle $curl
     * @param string $response_body
     * @return Response
     */
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