<?php

namespace Fazed\FazedHttp;

use Fazed\FazedHttp\Response;
use Fazed\FazedHttp\HeaderTrait;
use Fazed\FazedHttp\CookieTrait;

class Request
{
    use HeaderTrait, CookieTrait;

    /**
     * @var array
     */
    private $requestOptions = [
        CURLOPT_HEADER         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ];

    /**
     * @var string
     */
    private $expects;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $body;

    /**
     * Create a new Request instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  string $body
     * @return $this
     */
    public function __construct($method, $url, $body = '', $options = [])
    {
        $this->method = $method;
        $this->body = $body;
        $this->url = $url;

        return $this;
    }

    /**
     * Staticly create a new CurlHelper instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  string $body
     * @return $this
     */
    public static function make($method, $url, $body = '', $options = [])
    {
        return new self($method, $url, $body);
    }

    /**
     * Send a GET request with minimal configuration.
     *
     * @param  string $url
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function sendGetRequest($url, $headers = [], $cookies = [], $options = [])
    {
        return static::makeQuickRequest('GET', $url, '', $headers, $cookies, $options)->send();
    }

    /**
     * Send a POST request with minimal configuration.
     *
     * @param  string $url
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function sendPostRequest($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeQuickRequest('POST', $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Send a PUT request with minimal configuration.
     *
     * @param  string $url
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function sendPutRequest($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeQuickRequest('PUT', $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Send a DELETE request with minimal configuration.
     *
     * @param  string $url
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function sendDeleteRequest($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeQuickRequest('DELETE', $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Create a new CurlHelper instance for quick access.
     *
     * @param  string $method
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return $this
     */
    public static function makeQuickRequest($method, $url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::make($method, $url, $body, $options)
            ->setHeaders($headers)
            ->setCookies($cookies);
    }

    /**
     * Set the expected response format.
     *
     * @param  string $expectation
     * @return $this
     */
    public function expects($expectation)
    {
        $this->expects = $expectation;

        return $this;
    }

    /**
     * Set the expected response format to JSON.
     *
     * @return $this
     */
    public function expectsJson()
    {
        $this->expects = 'json';

        return $this;
    }

    /**
     * Set the body for the request;
     *
     * @param  string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set an additional curl option.
     *
     * @param  int    $option
     * @param  string $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->requestOptions[$option] = $value;

        return $this;
    }

    /**
     * Set additional curl options.
     *
     * @param  array $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        foreach ($options as $key=>$value) {
            $this->requestOptions[$key] = $value;
        }

        return $this;
    }

    /**
     * Execute the request.
     *
     * @return mixed
     */
    public function send()
    {
        $request = $this->prepareRequest();

        $response = curl_exec($request);

        return new Response($response, $this->expects, $request);
    }

    /**
     * Prepares the current request before sending.
     *
     * @return cURL
     */
    private function prepareRequest()
    {
        $request = curl_init();

        $this->setOption(CURLOPT_URL, $this->url);
        $this->setOption(CURLOPT_CUSTOMREQUEST, $this->method);
        $this->setOption(CURLOPT_HTTPHEADER, $this->getFormattedHeaderArray());
        $this->setOption(CURLOPT_COOKIE, $this->getCookieString());

        if ($this->method !== 'GET') {
            $this->setOption(CURLOPT_POST, true);
            $this->setOption(CURLOPT_POSTFIELDS, $this->body);
            $this->putHeader('Content-Length', strlen($this->body));
        }

        curl_setopt_array($request, $this->requestOptions);

        return $request;
    }
}
