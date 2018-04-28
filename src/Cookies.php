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

    protected $domain;
    protected $path     = '/';
    protected $secure   = false;
    protected $httpOnly = true;

    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function set($name, $value, $minutes = 60)
    {
        $options = [
            'domain' => $this->domain,
            'path' => $this->path,
            'secure' => $this->secure,
            'http' => $this->httpOnly
        ];

        $this->addedCookies[] = new Cookie($name, $value, $minutes, $options);
    }

    public function get($key, $default = null)
    {
        if (!$this->exists($key)) { return $this->cookies[$key]; }

        return $default;
    }

    public function exists($key)
    {
        return !empty($this->cookies[$key]);
    }

    public function clear($key)
    {
        $this->set($key, null, -2628000);
    }

    public function permanent($key, $value)
    {
        $this->set($key, $value, 2628000);
    }

    public function setHeaders(ResponseInterface $response): ResponseInterface
    {
        //TODO: Set-Cookie headers
        return $response;
    }
}
