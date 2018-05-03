<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Cookies;


class CookieJar
{
    private $cookies;
    private $modified = [];

    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function set(Cookie $cookie)
    {
        $name = $cookie->name();

        $this->cookies[$name] = $cookie->value();
        $this->modified[]     = $cookie;
    }

    public function get($key, $default = null)
    {
        if (!$this->exists($key)) { return $default; }

        return $this->cookies[$key];
    }

    public function exists($key)
    {
        return isset($this->cookies[$key]);
    }

    public function clear($key)
    {
        if (!$this->exists($key)) { return; }

        $this->set(new Cookie($key, null, -2628000));
    }

    public function permanent($key, $value)
    {
        $this->set(new Cookie($key, $value, 2628000));
    }

    public function responseCookies()
    {
        return $this->modified;
    }
}
