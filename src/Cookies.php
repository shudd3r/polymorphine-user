<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User;


class Cookies
{
    protected $domain;
    protected $path     = '/';
    protected $secure   = false;
    protected $httpOnly = true;

    public function set($name, $value, $minutes = 60)
    {
        $expiry = time() + ($minutes * 60);

        setcookie($name, $value, $expiry, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }

    public function get($key, $default = null)
    {
        if (!$this->exists($key)) { return $_COOKIE[$key]; }

        return $default;
    }

    public function exists($key)
    {
        return !empty($_COOKIE[$key]);
    }

    public function clear($key)
    {
        $this->set($key, null, -2628000);
    }

    public function permanent($key, $value)
    {
        $this->set($key, $value, 2628000);
    }
}
