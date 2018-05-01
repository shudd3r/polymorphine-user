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

use Iterator;


class CookieJar implements Iterator
{
    private $cookies;
    private $modified = [];
    private $pointer  = 0;

    /**
     * @param Cookie[] $cookies
     */
    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function set(Cookie $cookie)
    {
        $name = $cookie->name();

        $this->cookies[$name] = $cookie;
        $this->modified[]     = $name;
    }

    public function getValue($key, $default = null)
    {
        if (!$this->exists($key)) { return $default; }

        return $this->cookies[$key]->value();
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

    public function current()
    {
        $name = $this->modified[$this->pointer];

        return $this->cookies[$name];
    }

    public function next()
    {
        return $this->pointer++;
    }

    public function key()
    {
        return $this->pointer;
    }

    public function valid()
    {
        return isset($this->modified[$this->pointer]);
    }

    public function rewind()
    {
        $this->pointer = 0;
    }
}
