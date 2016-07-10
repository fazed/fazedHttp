<?php

namespace Fazed\FazedHttp;

use Exception;
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
     * @var string|array
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
     * @param  array   $options
     */
    public function __construct($method, $url, $body = '', $options = [])
    {
        $this->url = $url;
        $this->body = $body;
        $this->method = $method;

        $this->requestOptions = array_replace($this->requestOptions, $options);
    }

    /**
     * Statically create a new Request instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  string $body
     * @param  array  $options
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
     * @param  string $url
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
     * Compose a GET request.
     *
     * @param  string $url
     * @return Request
     */
    public static function get($url)
    {
        return static::makeRequest(static::METHOD_GET, $url);
    }

    /**
     * Compose a POST request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function post($url, $body = '')
    {
        return static::makeRequest(static::METHOD_POST, $url, $body);
    }

    /**
     * Compose a PUT request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function put($url, $body = '')
    {
        return static::makeRequest(static::METHOD_PUT, $url, $body);
    }

    /**
     * Compose a PATCH request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function patch($url, $body = '')
    {
        return static::makeRequest(static::METHOD_PATCH, $url, $body);
    }

    /**
     * Compose a DELETE request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function delete($url, $body = '')
    {
        return static::makeRequest(static::METHOD_DELETE, $url, $body);
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
     * Set the body for the request.
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
            throw new Exception(sprintf('An error occurred: %s', curl_error($request)));
        }

        curl_close($request);

        return Response::make($response, $this->expectsType, $requestInfo);
    }

    /**
     * Prepares the current request before sending.
     *
     * @return resource
     */
    private function prepareRequest()
    {
        $request = curl_init();

        $this->disableSslChecks();

        $this->setOption(CURLOPT_URL, $this->url);
        $this->setOption(CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->method !== static::METHOD_GET) {
            $payload = is_array($this->body) ? $this->inflatePayload() : $this->body;

            $this->setOption(CURLOPT_POSTFIELDS, $payload);
            $this->putHeader('Content-Length', strlen($payload));

            // TODO: count for non-array payloads
            if (is_array($this->body)) {
                $this->setOption(CURLOPT_POST, count($this->body));
            }

            if ($this->sendsType) {
                $this->putHeader('Content-Type', $this->resolveContentType());
            }
        }

        if (count($this->getCookies())) {
            $this->setOption(CURLOPT_COOKIE, $this->makeCookieHeaderString());
        }

        if (count($this->getHeaders())) {
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
            case 'xml' : return 'application/xml';  break;
            case 'json': return 'application/json'; break;
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

    /**
     * Inflate the payload according to the set content-type.
     *
     * @return string
     */
    private function inflatePayload()
    {
        $payload = '';

        switch ($this->sendsType) {
            // TODO: create an XML-case.
            case 'json': $payload = json_encode($this->body); break;
            default: $this->makePayloadString(); break;
        }

        return $payload;
    }

    /**
     * Make a string representation of the body.
     *
     * @return string
     */
    private function makePayloadString()
    {
        $payload = '';

        foreach($this->body as $key=>$value) {
            $payload .= sprintf('%s=%s&', $key, $value);
        }

        return rtrim($payload, '&');
    }
}
