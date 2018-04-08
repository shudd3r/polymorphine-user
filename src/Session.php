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


interface Session
{
    public function get(string $key, $default = null);

    public function set(string $key, $value = null): void;

    public function exists(string $key): bool;

    public function clear(string $key): void;
}
