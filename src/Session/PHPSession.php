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


class PHPSession implements Session
{
    public function __construct()
    {
        session_start();
    }

    public function get(string $key, $default = null)
    {
        if (!$this->exists($key)) { return $default; }

        return $_SESSION[$key];
    }

    public function set(string $key, $value = null): void
    {
        $_SESSION[$key] = $value;
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function clear(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
