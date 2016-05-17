<?php

namespace Fazed\FazedHttp;

use Exception;
use Fazed\FazedHttp\Abstracts\AbstractHttp;
use Fazed\FazedHttp\Contracts\ResponseContract;

class Response extends AbstractHttp implements ResponseContract
{
    /**
     * @var string
     */
    private $rawHeader;

    /**
     * @var string
     */
    private $rawBody;

    /**
     * @var array|bool
     */
    private $responseInfo;

    /**
     * @var int
     */
    private $headerSize = 0;

    /**
     * @var string
     */
    private $expectedType = null;

    /**
     * @var string
     */
    private $responseType = null;

    /**
     * @var int
     */
    private $httpCode = null;

    /**
     * Create a new Response instance.
     *
     * @param  string $data
     * @param  string $expectedType
     * @param  array  $info
     * @throws Exception
     */
    public function __construct($data, $expectedType, $info)
    {
        $this->responseInfo = $info;
        $this->parseResponseInfo();

        $this->rawBody = trim(substr($data, $this->headerSize));
        $this->rawHeader = trim(substr($data, 0, $this->headerSize));
        $this->expectedType = $expectedType;

        if ($this->getHttpCode() === 304) {
            throw new Exception('The connection to the server timed out.');
        }

        $this->parseHeadersFromResponse();
        $this->parseCookiesFromResponse();
    }

    /**
     * Staticly create a new Response instance.
     *
     * @param  string $data
     * @param  string $expectedType
     * @param  mixed  $info
     * @return $this
     */
    public static function make($data, $expectedType, $info)
    {
        return new self($data, $expectedType, $info);
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
     * Get all of the request info.
     *
     * @return array|bool
     */
    public function getRequestInfo()
    {
        return $this->requestInfo;
    }

    /**
     * Get info from the request by its key.
     *
     * @param  string $key
     * @return mixed
     */
    public function getRequestInfoByKey($key)
    {
        if (array_key_exists($key, $this->requestInfo)) {
            return $this->requestInfo[$key];
        }

        return false;
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
     * Parse additional information from the response.
     */
    private function parseResponseInfo()
    {
        if ($this->responseInfo !== false) {
            $this->httpCode = $this->responseInfo['http_code'];
            $this->headerSize = $this->responseInfo['header_size'];
            $this->responseType = $this->responseInfo['content_type'];
        }
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
     */
    private function parseHeadersFromResponse()
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
    }

    /**
     * Put cookies from the response into the cookiejar.
     */
    private function parseCookiesFromResponse()
    {
        if (preg_match_all('/(?:Set-Cookie:\s)(.+)\n/', $this->rawHeader, $cookies, PREG_SET_ORDER) === false) {
            return;
        }

        $this->truncateCookieJar();

        foreach ($cookies as $cookie) {
            $cookie = $cookie[1];
            $cookieParts = explode(';', $cookie);
            $realCookieParts = [];

            array_walk($cookieParts, function (&$item) {
                $item = trim($item);
            });

            foreach ($cookieParts as $cookiePart) {
                if (($keyPosition = strpos($cookiePart, '=')) !== false) {
                    list($key, $value) = [
                        substr($cookiePart, 0, $keyPosition),
                        substr($cookiePart, ($keyPosition + 1))
                    ];

                    $realCookieParts[$key] = $value;
                } else {
                    $realCookieParts['appended'][] = $cookiePart;
                }
            }

            if (count($realCookieParts) > 1) {
                $cookieKey = array_keys($realCookieParts)[0];
                $tempValue = $realCookieParts[$cookieKey];
                unset($realCookieParts[$cookieKey]);

                $this->putCookie($cookieKey, array_merge(
                    ['value' => $tempValue],
                    $realCookieParts
                ));
            } else {
                $cookieKey = array_keys($realCookieParts)[0];
                $cookieValue = $realCookieParts[$cookieKey];

                $this->putCookie($cookieKey, $cookieValue);
            }
        }
    }
}
