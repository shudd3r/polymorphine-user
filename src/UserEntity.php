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


interface UserEntity
{
    public function id(): string;

    public function isLoggedIn(): bool;

    public function hasRole($role): bool;

    public function isAllowed($action): bool;

    public function profile($key = null);

    public function settings($key = null);
}
