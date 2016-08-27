<?php

namespace Fazed\FazedHttp;

use Carbon\Carbon;

class Cookie
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var null|string
     */
    protected $domain = null;

    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * @var Carbon|null
     */
    protected $expires = null;

    /**
     * @var array
     */
    protected $subCookies = [];

    /**
     * Cookie constructor.
     *
     * @param string      $name
     * @param string      $value
     * @param string      $path
     * @param Carbon|null $expires
     * @param string|null $domain
     * @param bool        $secure
     * @param bool        $httpOnly
     */
    public function __construct($name, $value, $path = '/', $expires = null, $domain = null, $secure = false, $httpOnly = false)
    {
        $this->name     = $name;
        $this->path     = $path;
        $this->value    = $value;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->expires  = $expires;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Make a new Cookie instance.
     *
     * @param  string      $name
     * @param  string      $value
     * @param  string      $path
     * @param  Carbon|null $expires
     * @param  string|null $domain
     * @param  bool        $secure
     * @param  bool        $httpOnly
     * @return Cookie
     */
    public function make($name, $value, $path = '/', $expires = null, $domain = null, $secure = true, $httpOnly = false)
    {
        return new static($name, $value, $path, $expires, $domain, $secure, $httpOnly);
    }

    /**
     * Add a sub-cookie to the cookie.
     *
     * @param  string $key
     * @param  string $value
     * @return $this
     */
    public function addSubCookie($key, $value)
    {
        $this->subCookies[$key] = $value;

        return $this;
    }

    /**
     * Add multiple sub-cookies to the cookie.
     *
     * Array format must represent a key-value structure:
     * [ <cookie-key> => <cookie-value> ]
     *
     * @param  array $cookies
     * @return $this
     */
    public function addSubCookies(array $cookies)
    {
        foreach ($cookies as $key=>$value) {
            $this->subCookies[$key] = $value;
        }

        return $this;
    }

    /**
     * Delete a sub-cookie from the cookie by its key.
     *
     * @param  string $key
     * @return $this
     */
    public function deleteSubCookie($key)
    {
        if (array_key_exists($key, $this->subCookies)) {
            unset($this->subCookies[$key]);
        }

        return $this;
    }

    /**
     * Return only the key-value representation of the cookie.
     *
     * @return string
     */
    public function getKeyValue()
    {
        return sprintf('%s=%s', $this->name, $this->value);
    }

    /**
     * Generate a string representation of the cookie.
     *
     * @return string
     */
    public function __toString()
    {
        $str = sprintf('%s=%s', $this->name, $this->value);

        if ($this->expires instanceof Carbon) {
            $str .= sprintf('; expires=%s', $this->expires->format('D, d M Y H:i:s e'));
        }

        if ($this->domain) $str .= sprintf('; domain=', $this->domain);

        $str .= sprintf('; path=', $this->path);

        if ($this->secure) $str .= '; secure';
        if ($this->httpOnly) $str .= '; HttpOnly';

        return $str;
    }
}