<?php

namespace Fazed\FazedHttp\Contracts;

interface HeaderContract
{
    /**
     * Set the authorization header, for humans.
     *
     * @param string $type
     * @param string $digest
     */
    public function setAuthorizationHeader($type, $digest);

    /**
     * Get all of the headers in the header collection.
     */
    public function getHeaders();

    /**
     * Get a header from the header collection.
     *
     * @param mixed $header
     */
    public function getHeader($header);

    /**
     * Set which headers are in the header collection.
     *
     * @param array  $headers
     */
    public function setHeaders(array $headers);

    /**
     * Add a header to the header collection crushing
     * the header with the same identifier.
     *
     * @param mixed $header
     * @param mixed $value
     */
    public function putHeader($header, $value);

    /**
     * Add a single header to the header collection.
     *
     * @param mixed $header
     * @param mixed $value
     */
    public function appendHeader($header, $value);

    /**
     * Delete the oldest header put in the collection.
     */
    public function shiftHeader();

    /**
     * Delete the newest header put into the collection.
     */
    public function popHeader();

    /**
     * Delete a header from the collection.
     *
     * @param string $header
     */
    public function deleteHeader($header);

    /**
     * Truncate the header collection.
     */
    public function truncateHeaderCollection();

    /**
     * Create a formatted string of the available headers.
     */
    public function makeFormattedHeaderString();

    /**
     * Create an array of formatted header strings.
     */
    public function makeFormattedHeaderArray();
}
