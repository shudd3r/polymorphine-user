<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Session;

use Polymorphine\User\Cookies\CookieJar;
use Polymorphine\User\Cookies\Cookie;
use Polymorphine\User\Session;
use RuntimeException;


class ServerAPISession implements Session
{
    private $cookies;
    private $sessionData;

    public function __construct(CookieJar $cookies)
    {
        $this->cookies = $cookies;

        if (session_status() !== PHP_SESSION_NONE) {
            throw new RuntimeException('Session started outside object context');
        }

        session_start();
        $this->sessionData = $_SESSION;

        if (!$this->cookies->exists(session_name())) {
            $this->cookies->set(new Cookie(session_name(), session_id()));
        }
    }

    public function token(): array
    {
        return [session_name() => session_id()];
    }

    public function get(string $key, $default = null)
    {
        if (!$this->exists($key)) { return $default; }

        return $this->sessionData[$key];
    }

    public function set(string $key, $value = null): void
    {
        $this->sessionData[$key] = $value;
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->sessionData);
    }

    public function clear(string $key): void
    {
        unset($this->sessionData[$key]);
    }

    public function __destruct()
    {
        $_SESSION = $this->sessionData;
    }
}
