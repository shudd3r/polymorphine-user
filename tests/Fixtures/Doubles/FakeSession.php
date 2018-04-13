<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Fixtures\Doubles;

use Polymorphine\User\Session;


class FakeSession implements Session
{
    private $sessionData;

    public function __construct(array $session = [])
    {
        $this->sessionData = $session;
    }

    public function get(string $key, $default = null)
    {
        return $this->sessionData[$key] ?? $default;
    }

    public function set(string $key, $value = null): void
    {
        $this->sessionData[$key] = $value;
    }

    public function exists(string $key): bool
    {
        return isset($this->sessionData[$key]);
    }

    public function clear(string $key): void
    {
        unset($this->sessionData[$key]);
    }
}
