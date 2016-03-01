<?php

namespace Fazed\FazedHttp;

use Exception;
use Fazed\FazedHttp\Response;
use Fazed\FazedHttp\HeaderTrait;
use Fazed\FazedHttp\CookieTrait;

class Request
{
    use HeaderTrait, CookieTrait;

    /**
     * Constants
     */
    const REQUEST_GET    = 'GET';
    const REQUEST_POST   = 'POST';
    const REQUEST_PUT    = 'PUT';
    const REQUEST_PATCH  = 'PATCH';
    const REQUEST_DELETE = 'DELETE';

    /**
     * @var array
     */
    private $requestOptions = [
        CURLOPT_HEADER         => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ];

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
     * @var string
     */
    private $expectsType;

    /**
     * @var string
     */
    private $sendsType;

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

        $this->requestOptions = array_replace($this->requestOptions, $options);

        return $this;
    }

    /**
     * Staticly create a new Request instance.
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
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function sendGetRequest($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeQuickRequest(REQUEST_GET, $url, $body, $headers, $cookies, $options)->send();
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
        return static::makeQuickRequest(REQUEST_POST, $url, $body, $headers, $cookies, $options)->send();
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
        return static::makeQuickRequest(REQUEST_PUT, $url, $body, $headers, $cookies, $options)->send();
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
        return static::makeQuickRequest(REQUEST_DELETE, $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Create a new Request instance for quick access.
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
     * Set the referer of the request.
     *
     * @param  string $referer
     * @return $this
     */
    public function setReferer($referer)
    {
        $this->setOption(CURLOPT_REFERER, $referer);

        return $this;
    }

    /**
     * Set the request content mimetype.
     *
     * @param  string $type
     * @return $this
     */
    public function sends($type)
    {
        $this->sendsType = $type;

        return $this;
    }

    /**
     * Set the request content mimetype to JSON.
     *
     * @return $this
     */
    public function sendsJson()
    {
        $this->sends('json');

        return $this;
    }

    /**
     * Set the request content mimetype to XML.
     *
     * @return $this
     */
    public function sendsXml()
    {
        $this->sends('xml');

        return $this;
    }

    /**
     * Set the expected response format.
     *
     * @param  string $type
     * @return $this
     */
    public function expects($type)
    {
        $this->expectsType = $type;

        return $this;
    }

    /**
     * Set the expected response format to JSON.
     *
     * @return $this
     */
    public function expectsJson()
    {
        $this->expects('json');

        return $this;
    }

    /**
     * Set the expected response format to XML.
     *
     * @return $this
     */
    public function expectsXml()
    {
        $this->expects('xml');

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
    public function setOptions($options)
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
     * @throws Exception
     */
    public function send()
    {
        $request = $this->prepareRequest();

        $response = curl_exec($request);

        if ($errorNo = curl_errno($request)) {
            throw new Exception(sprintf('An error occured: %s', curl_error($request)));
        }

        return Response::make($response, $this->expectsType, $request);
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

        if ($this->method !== REQUEST_GET) {
            $this->setOption(CURLOPT_POSTFIELDS, $this->body);

            $this->putHeader('Content-Length', strlen($this->body));
            if ($this->sendsType) $this->putHeader('Content-Type', $this->sendsType);
        }

        if (sizeof($this->getCookies())) $this->setOption(CURLOPT_COOKIE, $this->getCookieString());
        if (sizeof($this->getHeaders())) $this->setOption(CURLOPT_HTTPHEADER, $this->getFormattedHeaderArray());

        curl_setopt_array($request, $this->requestOptions);

        return $request;
    }

    /**
     * Resolve the content type for the current request.
     *
     * @return mixed
     */
    private function resolveContentType()
    {
        switch ($this->sendsType) {
            case 'json': return 'application/json'; break;
            case 'xml' : return 'application/xml';  break;
        }

        return null;
    }
}
