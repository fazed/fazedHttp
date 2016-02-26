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
     * @param  string $data
     * @param  string $expectedType
     * @param  cURL   $channel
     */
    public function __construct($data, $expectedType, &$channel)
    {
        $this->httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        $this->headerSize = curl_getinfo($channel, CURLINFO_HEADER_SIZE);
        $this->responseType = curl_getinfo($channel, CURLINFO_CONTENT_TYPE);

        $this->rawBody = trim(substr($data, $this->headerSize));
        $this->rawHeader = trim(substr($data, 0, $this->headerSize));
        $this->expectedType = $expectedType;

        $this->getHeadersFromResponse($data, $this->headerSize);
        $this->getCookiesFromResponse($data, $this->headerSize);

        curl_close($channel);
    }

    /**
     * Staticly create a new Response instance.
     *
     * @param  string $data
     * @param  string $expectedType
     * @param  cURL   $channel
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
     * Fill up the header collection with headers from the response.
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

                    $this->putHeader($headerKey, $headerValue);
                }
            }
        }

        return $this;
    }

    /**
     * Fill up the cookiejar with cookies from the response.
     * 
     * @return $this
     */
    private function getCookiesFromResponse()
    {
        if (preg_match_all('/(?:Set-Cookie:\s)(.+)\=(.+)?\;/', $this->rawHeader, $cookies) !== false) {
            $this->emptyCookieJar();

            foreach ($cookies as $cookie) {
                if (sizeof($cookie)) {
                    $this->appendCookie($cookie[0], $cookie[1] ?? '');
                }
            }
        }

        return $this;
    }
}