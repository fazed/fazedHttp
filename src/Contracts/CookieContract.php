<?php

namespace Fazed\FazedHttp\Contracts;

interface CookieContract
{
    /**
     * Get all of the cookies in the cookiejar.
     */
    public function getCookies();

    /**
     * Get a cookie from the cookiejar.
     *
     * @param mixed $cookie
     */
    public function getCookie($cookie);

    /**
     * Get the value of a cookie from the cookiejar.
     *
     * @param string $cookie
     */
    public function getCookieValue($cookie);

    /**
     * Set which cookies are in the cookie jar.
     *
     * @param array $cookies
     */
    public function setCookies(array $cookies);

    /**
     * Add a cookie to the cookiejar crushing
     * the cookie with the same identifier.
     *
     * @param mixed $cookie
     * @param mixed $value
     */
    public function putCookie($cookie, $value);

    /**
     * Add a single cookie to the cookiejar.
     *
     * @param mixed $cookie
     * @param mixed $value
     */
    public function appendCookie($cookie, $value);

    /**
     * Delete the oldest cookie put in the jar.
     */
    public function shiftCookie();

    /**
     * Delete the newest cookie put into the jar.
     */
    public function popCookie();

    /**
     * Delete a cookie from the cookiejar.
     *
     * @param  string $cookie
     */
    public function deleteCookie($cookie);

    /**
     * Truncate the cookiejar.
     */
    public function truncateCookieJar();

    /**
     * Create a string of the available cookies.
     */
    public function makeCookieHeaderString();
}
