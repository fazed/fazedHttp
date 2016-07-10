<?php

namespace Fazed\FazedHttp\Contracts;

interface ResponseContract
{
    /**
     * Statically create a new Response instance.
     *
     * @param string   $data
     * @param string   $expectedType
     * @param resource $channel
     */
    public static function make($data, $expectedType, $channel);

    /**
     * Return the raw header of the response.
     */
    public function getRawHeader();

    /**
     * Return the raw body of the response.
     */
    public function getRawBody();

    /**
     * Return the body of the response.
     */
    public function getBody();

    /**
     * Return the http code of the response.
     */
    public function getHttpCode();

    /**
     * Get all of the request info.
     */
    public function getRequestInfo();

    /**
     * Get info from the request by its key.
     *
     * @param string $key
     */
    public function getRequestInfoByKey($key);
}
