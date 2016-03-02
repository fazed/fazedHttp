<?php

namespace Fazed\FazedHttp;

use Exception;
use Fazed\FazedHttp\HeaderTrait;
use Fazed\FazedHttp\CookieTrait;

class Response
{
    use CookieTrait, HeaderTrait;

    /**
     * @var string
     */
    private $rawHeader;

    /**
     * @var string
     */
    private $rawBody;

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
     * 
     * @param  string   $data
     * @param  string   $expectedType
     * @param  resource $channel
     * @throws Exception
     */
    public function __construct($data, $expectedType, &$channel)
    {
        $this->httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        $this->headerSize = curl_getinfo($channel, CURLINFO_HEADER_SIZE);
        $this->responseType = curl_getinfo($channel, CURLINFO_CONTENT_TYPE);

        $this->rawBody = trim(substr($data, $this->headerSize));
        $this->rawHeader = trim(substr($data, 0, $this->headerSize));
        $this->expectedType = $expectedType;

        if ($this->getHttpCode() === 304) {
            throw new Exception('The connection to the server timed out.');
        }

        $this->getHeadersFromResponse();
        $this->getCookiesFromResponse();

        curl_close($channel);
    }

    /**
     * Staticly create a new Response instance.
     *
     * @param  string   $data
     * @param  string   $expectedType
     * @param  resource $channel
     * @return $this
     */
    public static function make($data, $expectedType, &$channel)
    {
        return new self($data, $expectedType, $channel);
    }

    /**
     * Return the raw header of the response.
     *
     * @return string
     */
    public function getRawHeader()
    {
        return $this->rawHeader;
    }

    /**
     * Return the raw body of the response.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
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
     * Return the http code of the response.
     *
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
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
            case 'json': return json_decode($this->rawBody); break;
            case 'xml' : return simplexml_load_string($this->rawBody); break;
            default    : return $this->rawBody; break;
        }
    }

    /**
     * Add headers from the response to the header collection.
     *
     * @return $this
     */
    private function getHeadersFromResponse()
    {
        $headerLines = explode("\r\n\r\n", $this->rawHeader);

        foreach ($headerLines as $headerLine) {
            $header = explode("\r\n", $headerLine);

            foreach ($header as $value) {
                $firstColonIndex = strpos($value, ':');

                if ($firstColonIndex !== false) {
                    $headerKey = trim(substr($value, 0, $firstColonIndex));
                    $headerValue = trim(substr($value, ($firstColonIndex + 1)));

                    if ($headerKey !== 'Set-Cookie') {
                        $this->putHeader($headerKey, $headerValue);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Put cookies from the response into the cookiejar.
     * 
     * @return $this
     */
    private function getCookiesFromResponse()
    {
        if (preg_match_all('/(?:Set-Cookie:\s)(.+)\=(.+)?\;/', $this->rawHeader, $cookies, PREG_SET_ORDER) !== false) {
            $this->emptyCookieJar();

            foreach ($cookies as $cookie) {
                if (sizeof($cookie)) {
                    $this->putCookie($cookie[1], $cookie[2] ?: '');
                }
            }
        }

        return $this;
    }
}
