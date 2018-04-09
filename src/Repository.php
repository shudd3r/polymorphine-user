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


interface Repository
{
    public function getUserById(string $id): UserEntity;
    public function getUserByCookieToken(string $id): UserEntity;
    public function guestUser(): UserEntity;
}