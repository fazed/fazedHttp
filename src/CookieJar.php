<?php

namespace Fazed\FazedHttp;

class CookieJar
{
    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * CookieJar constructor.
     *
     * @param array $cookies
     */
    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Add a cookie to the jar.
     *
     * @param  Cookie $cookie
     * @param  bool   $override
     * @return $this
     */
    public function putCookie(Cookie $cookie, $override = false)
    {
        $duplicate = false;

        foreach ($this->cookies as $existingCookie) {
            if (strcmp($existingCookie->__toString(), $cookie->__toString()) === 0) {
                $duplicate = true;
                break;
            }
        }

        if (! $duplicate || ($duplicate && $override)) {
            $this->cookies[] = $cookie;
        }

        return $this;
    }

    /**
     * Return all of the cookies in a single string (simple).
     *
     * @return string
     */
    public function getCookieString()
    {
        $cookies = array_map(function ($item) {
            return $item->getKeyValue();
        }, $this->cookies);

        return implode('; ', $cookies);
    }
}
