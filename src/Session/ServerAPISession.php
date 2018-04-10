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

use Polymorphine\User\Session;
use RuntimeException;


class ServerAPISession implements Session
{
    private $sessionData;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            throw new RuntimeException('Session started outside object context');
        }

        session_start();
        $this->sessionData = $_SESSION;
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
