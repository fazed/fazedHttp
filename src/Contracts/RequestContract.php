<?php

namespace Fazed\FazedHttp\Contracts;

interface RequestContract
{
    /**
     * Staticly create a new Request instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function make($method, $url, $body, $options);

    /**
     * Create a new Request instance for quick access.
     *
     * @param  string $method
     * @param  string $body
     * @param  array  $headers
     * @param  array  $cookies
     * @param  array  $options
     * @return Request
     */
    public static function makeRequest($method, $url, $body, $headers, $cookies, $options);

    /**
     * Compose a GET request.
     *
     * @param  string $url
     * @return Request
     */
    public static function get($url);

    /**
     * Compose a POST request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function post($url, $body = '');

    /**
     * Compose a PUT request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function put($url, $body = '');

    /**
     * Compose a PATCH request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function patch($url, $body = '');

    /**
     * Compose a DELETE request.
     *
     * @param  string $url
     * @param  string $body
     * @return Request
     */
    public static function delete($url, $body = '');

    /**
     * Set the HTTP-authentication method to basic.
     *
     * @param  string $username
     * @param  string $password
     * @return Request
     */
    public function setBasicAuthentication($username, $password);

    /**
     * Set the HTTP-authentication method to digest.
     *
     * @param  string $username
     * @param  string $password
     * @return Request
     */
    public function setDigestAuthentication($username, $password);

    /**
     * Set the referer of the request.
     *
     * @param  string $referer
     * @return Request
     */
    public function setReferer($referer);

    /**
     * Set the user agent of the request.
     *
     * @param  string $agent
     * @return Request
     */
    public function setUserAgent($agent);

    /**
     * Set the request content mimetype.
     *
     * @param  string $type
     * @return Request
     */
    public function sends($type);

    /**
     * Set the expected response format.
     *
     * @param  string $type
     * @return Request
     */
    public function expects($type);

    /**
     * Set the body for the request;
     *
     * @param  string $body
     * @return Request
     */
    public function setBody($body);

    /**
     * Set an additional curl option.
     *
     * @param  int    $option
     * @param  string $value
     * @return Request
     */
    public function setOption($option, $value);

    /**
     * Set additional curl options.
     *
     * @param  array $options
     * @return Request
     */
    public function setOptions(array $options);

    /**
     * Execute the request.
     *
     * @return Response
     * @throws Exception
     */
    public function send();
}
