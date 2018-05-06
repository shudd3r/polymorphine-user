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


use Polymorphine\User\Entity\AnonymousUser;
use Polymorphine\User\Repository;
use Polymorphine\User\UserEntity;


class FakeRepository implements Repository
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
    public function getUserById(string $id): UserEntity
    {
        return $this->id === $id ? new FakeUser($id) : $this->anonymousUser();
    }

    public function getUserByCookieToken(string $id): UserEntity
    {
        return $this->id === $id ? new FakeUser($id) : $this->anonymousUser();
    }

    public function anonymousUser(): UserEntity
    {
        return new AnonymousUser();
    }
}
