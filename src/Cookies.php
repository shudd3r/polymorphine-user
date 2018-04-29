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

use Psr\Http\Message\ResponseInterface;


class Cookies
{
    private $cookies;
    private $addedCookies = [];

    public function __construct(Cookie ...$cookies)
    {
        $this->cookies = $cookies;
    }

    public function set(Cookie $cookie)
    {
        $name = $cookie->name();
        $this->cookies[$name] = $cookie;
        $this->addedCookies[] = $name;
    }

    public function get($key, $default = null)
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
        $this->set(new Cookie($key, null, -2628000));
    }

    public function permanent($key, $value)
    {
        $this->set(new Cookie($key, $value, 2628000));
    }

    public function setHeaders(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->addedCookies as $cookieName) {
            $response = $response->withAddedHeader('Set-Cookie', $this->cookies[$cookieName]->headerLine());
        }

        return $response;
    }
}
