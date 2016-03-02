<?php

namespace Fazed\FazedHttp;

trait CookieTrait
{
    /**
     * @var array
     */
    private $cookieJar = [];

    /**
     * Get all of the cookies in the cookiejar.
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookieJar;
    }

    /**
     * Get a cookie from the cookiejar.
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
     * Add a cookie to the cookiejar crushing
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
     * Add multiple cookies to the cookiejar
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
     * Add a single cookie to the cookiejar.
     *
     * @param  mixed  $cookie
     * @param  mixed  $value
     * @return $this
     */
    public function appendCookie($cookie, $value)
    {
        if (! array_key_exists($cookie)) {
            $this->cookieJar[$cookie] = $value;
        }

        return $this;
    }

    /**
     * Add multiple cookies to the cookiejar.
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
     * Destroy the oldest cookie put in the jar.
     *
     * @return $this
     */
    public function shiftCookie()
    {
        $this->cookieJar = array_shift($this->cookieJar);

        return $this;
    }

    /**
     * Destroy the newest cookie put into the jar.
     *
     * @return $this
     */
    public function popCookie()
    {
        $this->cookieJar = array_pop($this->cookieJar);

        return $this;
    }

    /**
     * Empty the cookiejar.
     *
     * @return $this
     */
    public function emptyCookieJar()
    {
        $this->cookieJar = [];

        return $this;
    }

    /**
     * Create a string of the available cookies.
     *
     * @return string
     */
    private function makeCookieHeaderString()
    {
        $cookieString = '';

        foreach ($this->cookieJar as $cookie=>$value) {
            $cookieString .= sprintf('Set-Cookie: %s=%s;', $cookie, $value);
        }

        return $cookieString;
    }
}
