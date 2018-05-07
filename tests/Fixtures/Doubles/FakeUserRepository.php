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
use Polymorphine\User\AuthenticatedUser;


class FakeUserRepository implements Repository
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getUserById(string $id): AuthenticatedUser
    {
        return $this->id === $id ? new FakeUser($id) : $this->anonymousUser();
    }

    public function getUserByLoginForm(string $login, string $password, bool $remember): AuthenticatedUser
    {
        return $this->id === $login ? new FakeUser($login) : $this->anonymousUser();
    }

    public function getUserByCookieToken(string $token): AuthenticatedUser
    {
        return $this->id === $token ? new FakeUser($token) : $this->anonymousUser();
    }

    public function anonymousUser(): AuthenticatedUser
    {
        return new AnonymousUser();
    }
}
