<?php

namespace Fazed\FazedHttp;

use Fazed\FazedHttp\HeaderTrait;
use Fazed\FazedHttp\CookieTrait;

class Response
{
    use CookieTrait, HeaderTrait;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $expectedType;

    /**
     * @var string
     */
    private $responseType;

    /**
     * @var int
     */
    private $httpCode;

    /**
     * Create a new Response instance.
     */
    public function __construct($data, $expectedType, &$channel)
    {
        $this->httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        $this->responseType = curl_getinfo($channel, CURLINFO_CONTENT_TYPE);
        $this->headerSize = curl_getinfo($channel, CURLINFO_HEADER_SIZE);

        $this->body = substr($data, $this->headerSize);
        $this->expectedType = $expectedType;
        $this->originalResponse = $data;

        $this->getHeadersFromResponse($data, $this->headerSize);
        $this->getCookiesFromResponse($data, $this->headerSize);

        curl_close($channel);
    }

    /**
     * Staticly create a new Response instance.
     *
     * @return $this
     */
    public static function make()
    {
        return new self();
    }

    /**
     * Return the raw body of the response.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * Return the body of the response.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->formatByExpected();
    }

    /**
     * Return response body.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRawBody();
    }

    /**
     * Format the request response to the expected format.
     *
     * @param  string $response
     * @return mixed
     */
    private function formatByExpected()
    {
        switch ($this->expectedType) {
            case 'json'     : return json_decode($this->body); break;
            case 'jsonArray': return json_decode($this->body, true); break;
            case 'xml'      : return simplexml_load_string($this->body); break;
            default         : return $this->body; break;
        }
    }
}