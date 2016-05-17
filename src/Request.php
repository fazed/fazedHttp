<?php

namespace Fazed\FazedHttp;

use Exception;
use Fazed\FazedHttp\Response;
use Fazed\FazedHttp\Traits\SecurityTrait;
use Fazed\FazedHttp\Abstracts\AbstractHttp;
use Fazed\FazedHttp\Contracts\RequestContract;

class Request extends AbstractHttp implements RequestContract
{
    use SecurityTrait;

    /**
     * Constants
     */
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_PATCH  = 'PATCH';
    const METHOD_DELETE = 'DELETE';

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
     */
    public function __construct($method, $url, $body = '', $options = [])
    {
        $this->url = $url;
        $this->body = $body;
        $this->method = $method;

        $this->requestOptions = array_replace($this->requestOptions, $options);
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
        return new self($method, $url, $body, $options);
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
    public static function makeRequest($method, $url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::make($method, $url, $body, $options)
            ->setHeaders($headers)
            ->setCookies($cookies);
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
    public static function get($url, $headers = [], $cookies = [], $options = [])
    {
        return static::makeRequest(static::METHOD_GET, $url, '', $headers, $cookies, $options)->send();
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
    public static function post($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeRequest(static::POST, $url, $body, $headers, $cookies, $options)->send();
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
    public static function put($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeRequest(static::PUT, $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Send a PATCH request with minimal configuration.
     *
     * @param  string $url
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Response
     */
    public static function patch($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeRequest(static::PATCH, $url, $body, $headers, $cookies, $options)->send();
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
    public static function delete($url, $body = '', $headers = [], $cookies = [], $options = [])
    {
        return static::makeRequest(static::DELETE, $url, $body, $headers, $cookies, $options)->send();
    }

    /**
     * Set the HTTP-authentication method to basic.
     *
     * @param  string $username
     * @param  string $password
     * @return $this
     */
    public function setBasicAuthentication($username, $password)
    {
        return $this->prepareDefaultAuthentication(CURLAUTH_BASIC, $username, $password);
    }

    /**
     * Set the HTTP-authentication method to digest.
     *
     * @param  string $username
     * @param  string $password
     * @return $this
     */
    public function setDigestAuthentication($username, $password)
    {
        return $this->prepareDefaultAuthentication(CURLAUTH_DIGEST, $username, $password);
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
     * Set the user agent of the request.
     *
     * @param  string $agent
     * @return $this
     */
    public function setUserAgent($agent)
    {
        $this->setOption(CURLOPT_USERAGENT, $agent);

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
    public function setOptions(array $options)
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
        $requestInfo = curl_getinfo($request);

        if ($errorNo = curl_errno($request)) {
            throw new Exception(sprintf('An error occured: %s', curl_error($request)));
        }

        curl_close($request);

        return Response::make($response, $this->expectsType, $requestInfo);
    }

    /**
     * Prepares the current request before sending.
     *
     * @return cURL
     */
    private function prepareRequest()
    {
        $request = curl_init();

        $this->disableSslChecks();

        $this->setOption(CURLOPT_URL, $this->url);
        $this->setOption(CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->method !== static::METHOD_GET) {
            $this->setOption(CURLOPT_POSTFIELDS, $this->body);

            $this->putHeader('Content-Length', strlen($this->body));

            if ($this->sendsType) {
                $this->putHeader('Content-Type', $this->resolveContentType());
            }
        }

        if (sizeof($this->getCookies())) {
            $this->setOption(CURLOPT_COOKIE, $this->makeCookieHeaderString());
        }

        if (sizeof($this->getHeaders())) {
            $this->setOption(CURLOPT_HTTPHEADER, $this->makeFormattedHeaderArray());
        }

        $this->inflateRequestOptions($request);

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

    /**
     * Set the default HTTP-authentication method.
     *
     * @param  int    $type
     * @param  string $username
     * @param  string $password
     * @return $this
     */
    private function prepareDefaultAuthentication($type, $username, $password)
    {
        $this->setOption(CURLOPT_HTTPAUTH, $type);
        $this->setOption(CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));

        return $this;
    }

    /**
     * Set the options for the given cURL instance.
     *
     * @param resource $request
     */
    private function inflateRequestOptions($request)
    {
        if (ini_get('safe_mode')) {
            if (array_key_exists(CURLOPT_FOLLOWLOCATION, $this->requestOptions)) {
                unset($this->requestOptions[CURLOPT_FOLLOWLOCATION]);
            }
        }

        curl_setopt_array($request, $this->requestOptions);
    }
}
