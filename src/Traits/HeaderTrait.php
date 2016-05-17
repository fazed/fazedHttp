<?php

namespace Fazed\FazedHttp\Traits;

trait HeaderTrait
{
    /**
     * @var int
     */
    private $headerSize = 0;

    /**
     * @var array
     */
    private $headerCollection = [];

    /**
     * Set the authorization header, for humans.
     *
     * @param  string $type
     * @param  string $digest
     * @return $this
     */
    public function setAuthorizationHeader($type, $digest)
    {
        $this->putHeader('Authorization', sprintf('%s %s', $type, $digest));

        return $this;
    }

    /**
     * Get all of the headers in the header collection.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headerCollection;
    }

    /**
     * Get a header from the header collection.
     *
     * @param  mixed  $header
     * @return mixed
     */
    public function getHeader($header)
    {
        if (array_key_exists($header, $this->headerCollection)) {
            return $this->headerCollection[$header];
        }

        return null;
    }

    /**
     * Set which headers are in the header collection.
     *
     * @param  array  $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headerCollection = $headers;

        return $this;
    }

    /**
     * Add a header to the header collection crushing
     * the header with the same identifier.
     *
     * @param  mixed  $header
     * @param  mixed  $value
     * @return $this
     */
    public function putHeader($header, $value)
    {
        $this->headerCollection[$header] = $value;

        return $this;
    }

    /**
     * Add multiple headers to the header collection
     * crushing the existing headers with
     * the same identifier.
     *
     * @param  array  $headers
     * @return $this
     */
    public function putHeaders(array $headers)
    {
        foreach ($headers as $header=>$value) {
            $this->putHeader($header, $value);
        }

        return $this;
    }

    /**
     * Add a single header to the header collection.
     *
     * @param  mixed  $header
     * @param  mixed  $value
     * @return $this
     */
    public function appendHeader($header, $value)
    {
        if (! array_key_exists($header)) {
            $this->headerCollection[$header] = $value;
        }

        return $this;
    }

    /**
     * Add multiple headers to the header collection.
     *
     * @param  array  $headers
     * @return $this
     */
    public function appendHeaders(array $headers)
    {
        foreach ($headers as $header=>$value) {
            $this->appendHeader($header, $value);
        }

        return $this;
    }

    /**
     * Delete the oldest header put in the collection.
     *
     * @return $this
     */
    public function shiftHeader()
    {
        $this->headerCollection = array_shift($this->headerCollection);

        return $this;
    }

    /**
     * Delete the newest header put into the collection.
     *
     * @return $this
     */
    public function popHeader()
    {
        $this->headerCollection = array_pop($this->headerCollection);

        return $this;
    }

    /**
     * Delete a header from the collection.
     *
     * @param  string  $header
     * @return $this
     */
    public function deleteHeader($header)
    {
        if (array_key_exists($header, $this->headerCollection)) {
            unset($this->headerCollection[$header]);
        }

        return $this;
    }

    /**
     * Delete multiple headers from the collection.
     *
     * @param  array  $headers
     * @return $this
     */
    public function deleteHeaders(array $headers)
    {
        foreach ($headers as $header) {
            $this->deleteHeader($header);
        }

        return $this;
    }

    /**
     * Empty the header collection.
     *
     * @return $this
     */
    public function truncateHeaderCollection()
    {
        $this->headerCollection = [];

        return $this;
    }

    /**
     * Create a formatted string of the available headers.
     *
     * @return string
     */
    public function makeFormattedHeaderString()
    {
        $formattedString = '';

        foreach ($this->headerCollection as $header=>$value) {
            $formattedString .= sprintf('%s: %s; ', $header, $value);
        }

        return empty($formattedString) ? null : rtrim($formattedString);
    }

    /**
     * Create an array of formatted header strings.
     *
     * @return array
     */
    public function makeFormattedHeaderArray()
    {
        $formattedHeaders = [];

        foreach ($this->headerCollection as $header=>$value) {
            $formattedHeaders[] = sprintf('%s: %s', $header, $value);
        }

        return $formattedHeaders;
    }
}
