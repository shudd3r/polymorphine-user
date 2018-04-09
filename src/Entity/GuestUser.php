<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Entity;

use Polymorphine\User\UserEntity;


class GuestUser implements UserEntity
{
    public function id(): string
    {
        return '';
    }

    public function isLoggedIn(): bool
    {
        return false;
    }

    public function hasRole($role): bool
    {
        return false;
    }

    public function isAllowed($action): bool
    {
        return false;
    }

    public function profile($key = null)
    {
        return $key ? '' : [];
    }

    public function settings($key = null)
    {
        return $key ? '' : [];
    }
}
