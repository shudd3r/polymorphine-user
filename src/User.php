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


interface User
{
    public function id(): string;

    public function isLoggedIn(): bool;

    public function isAdmin(): bool;

    public function profile($name = null);

    public function config($key = null);
}
