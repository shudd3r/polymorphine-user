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


class SessionDataStorage
{
    private $sessionData;

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

    public function getAll(): array
    {
        return $this->sessionData;
    }
}