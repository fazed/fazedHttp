<?php

namespace Fazed\FazedHttp\Traits;

trait CookieTrait
{
    /**
     * @var array
     */
    private $cookieJar = [];

    /**
     * Get all of the cookies in the cookie jar.
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookieJar;
    }

    /**
     * Return whether the cookie exists.
     *
     * @param  string $cookie
     * @return bool
     */
    public function hasCookie($cookie)
    {
        return array_key_exists($cookie, $this->cookieJar);
    }

    /**
     * Get a cookie from the cookie jar.
     *
     * @param  mixed  $cookie
     * @return mixed
     */
    public function getCookie($cookie)
    {
        if (array_key_exists($cookie, $this->cookieJar)) {
            return $this->cookieJar[$cookie];
        }

        return null;
    }

    /**
     * Get the value of a cookie from the cookie jar.
     *
     * @param  string  $cookie
     * @return mixed
     */
    public function getCookieValue($cookie)
    {
        if (array_key_exists($cookie, $this->cookieJar)) {
            return is_array($this->cookieJar[$cookie])
                ? $this->cookieJar[$cookie]['value']
                : $this->cookieJar[$cookie];
        }

        return null;
    }

    /**
     * Set which cookies are in the cookie jar.
     *
     * @param  array $cookies
     * @return $this
     */
    public function setCookies(array $cookies)
    {
        $this->cookieJar = $cookies;

        return $this;
    }

    /**
     * Add a cookie to the cookie jar crushing
     * the cookie with the same identifier.
     *
     * @param  mixed  $cookie
     * @param  mixed  $value
     * @return $this
     */
    public function putCookie($cookie, $value)
    {
        $this->cookieJar[$cookie] = $value;

        return $this;
    }

    /**
     * Add multiple cookies to the cookie jar
     * crushing the existing cookies with
     * the same identifier.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function putCookies(array $cookies)
    {
        foreach ($cookies as $cookie=>$value) {
            $this->putCookie($cookie, $value);
        }

        return $this;
    }

    /**
     * Add a single cookie to the cookie jar.
     *
     * @param  mixed  $cookie
     * @param  mixed  $value
     * @return $this
     */
    public function appendCookie($cookie, $value)
    {
        if (! array_key_exists($cookie, $this->cookieJar)) {
            $this->cookieJar[$cookie] = $value;
        }

        return $this;
    }

    /**
     * Add multiple cookies to the cookie jar.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function appendCookies(array $cookies)
    {
        foreach ($cookies as $cookie=>$value) {
            $this->appendCookie($cookie, $value);
        }

        return $this;
    }

    /**
     * Delete the oldest cookie put in the jar.
     *
     * @return $this
     */
    public function shiftCookie()
    {
        $this->cookieJar = array_shift($this->cookieJar);

        return $this;
    }

    /**
     * Delete the newest cookie put into the jar.
     *
     * @return $this
     */
    public function popCookie()
    {
        $this->cookieJar = array_pop($this->cookieJar);

        return $this;
    }

    /**
     * Delete a cookie from the cookie jar.
     *
     * @param  string  $cookie
     * @return $this
     */
    public function deleteCookie($cookie)
    {
        if (array_key_exists($cookie, $this->cookieJar)) {
            unset($this->cookieJar[$cookie]);
        }

        return $this;
    }

    /**
     * Delete multiple cookies from the cookie jar.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function deleteCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->deleteCookie($cookie);
        }

        return $this;
    }

    /**
     * Truncate the cookie jar.
     *
     * @return $this
     */
    public function truncateCookieJar()
    {
        $this->cookieJar = [];

        return $this;
    }

    /**
     * Create a string of the available cookies.
     *
     * @return string
     */
    public function makeCookieHeaderString()
    {
        $cookieString = '';

        foreach ($this->cookieJar as $cookie=>$value) {
            $cookieString .= sprintf('%s=%s; ', $cookie, $value);
        }

        return rtrim($cookieString, '; ');
    }
}
