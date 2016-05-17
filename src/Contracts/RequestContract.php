<?php

namespace Fazed\FazedHttp\Contracts;

interface RequestContract
{
    /**
     * Staticly create a new Request instance.
     *
     * @param string $method
     * @param string $url
     * @param string $body
     */
    public static function make($method, $url, $body, $options);

    /**
     * Create a new Request instance for quick access.
     *
     * @param string $method
     * @param string $body
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function makeRequest($method, $url, $body, $headers, $cookies, $options);

    /**
     * Send a GET request with minimal configuration.
     *
     * @param string $url
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function get($url, $headers, $cookies, $options);

    /**
     * Send a POST request with minimal configuration.
     *
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function post($url, $body, $headers, $cookies, $options);

    /**
     * Send a PUT request with minimal configuration.
     *
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function put($url, $body, $headers, $cookies, $options);

    /**
     * Send a PATCH request with minimal configuration.
     *
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function patch($url, $body, $headers, $cookies, $options);

    /**
     * Send a DELETE request with minimal configuration.
     *
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param array  $cookies
     * @param array  $options
     */
    public static function delete($url, $body, $headers, $cookies, $options);

    /**
     * Set the HTTP-authentication method to basic.
     *
     * @param string $username
     * @param string $password
     */
    public function setBasicAuthentication($username, $password);

    /**
     * Set the HTTP-authentication method to digest.
     *
     * @param string $username
     * @param string $password
     */
    public function setDigestAuthentication($username, $password);

    /**
     * Set the referer of the request.
     *
     * @param string $referer
     */
    public function setReferer($referer);

    /**
     * Set the user agent of the request.
     *
     * @param string $agent
     */
    public function setUserAgent($agent);

    /**
     * Set the request content mimetype.
     *
     * @param string $type
     */
    public function sends($type);

    /**
     * Set the expected response format.
     *
     * @param string $type
     */
    public function expects($type);

    /**
     * Set the body for the request;
     *
     * @param string $body
     */
    public function setBody($body);

    /**
     * Set an additional curl option.
     *
     * @param int    $option
     * @param string $value
     */
    public function setOption($option, $value);

    /**
     * Set additional curl options.
     *
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Execute the request.
     *
     * @return mixed
     * @throws Exception
     */
    public function send();
}
