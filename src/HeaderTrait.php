<?php

namespace Fazed\FazedHttp;

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
     * Destory the oldest header put in the collection.
     *
     * @return $this
     */
    public function shiftHeader()
    {
        $this->headerCollection = array_shift($this->headerCollection);

        return $this;
    }

    /**
     * Destroy the newest header put into the collection.
     *
     * @return $this
     */
    public function popHeader()
    {
        $this->headerCollection = array_pop($this->headerCollection);

        return $this;
    }

    /**
     * Empty the header collection.
     *
     * @return $this
     */
    public function emptyHeaderJar()
    {
        $this->headerCollection = [];

        return $this;
    }

    /**
     * Fill up the header collection with headers
     * from an HTTP-response.
     *
     * @param  string  &$response
     * @param  int     $headerSize
     * @return $this
     */
    private function getHeadersFromResponse(&$response, $headerSize)
    {
        $actualHeader = substr($response, 0, $headerSize);
        $headerLines = explode("\r\n\r\n", $actualHeader);

        foreach ($headerLines as $headerLine) {
            $header = explode("\r\n", $headerLine);

            foreach ($header as $value) {
                $firstColonIndex = strpos($value, ':');

                if ($firstColonIndex !== false) {
                    $headerKey = trim(substr($value, 0, $firstColonIndex));
                    $headerValue = trim(substr($value, ($firstColonIndex + 1)));

                    $this->putHeader($headerKey, $headerValue);
                }
            }
        }

        return $this;
    }

    /**
     * Create a formatted string of the available headers.
     *
     * @return string
     */
    private function getFormattedHeaderString()
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
    private function getFormattedHeaderArray()
    {
        $formattedHeaders = [];

        foreach ($this->headerCollection as $header=>$value) {
            $formattedHeaders[] = sprintf('%s: %s', $header, $value);
        }

        return $formattedHeaders;
    }
}
