<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Doubles;

use Polymorphine\User\Repository;
use Polymorphine\User\AuthenticatedUser;
use Polymorphine\User\Data;
use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Data\DbRecord;


class FakeUsersRepository extends Repository
{
    public $token    = [];
    public $password = [];

    public $user;
    public $guest;

    public function __construct(FakeAuthUser $user = null)
    {
        $this->guest = new FakeAuthUser(0, 'Anonymous');
        $this->user  = $user ?: $this->guest;
    }

    public function getUser(Credentials $credentials): AuthenticatedUser
    {
        return $this->user;
    }

    public function getAnonymousUser(): AuthenticatedUser
    {
        return $this->guest;
    }

    public function setToken(int $id, string $tokenKey, string $token)
    {
        $this->token = ['id' => $id, 'tokenKey' => $tokenKey, 'token' => $token];
    }

    public function setPassword(int $id, string $password)
    {
        $this->password = ['id' => $id, 'password' => $password];
    }

    protected function record(Data $user): ?DbRecord
    {
        return null;
    }
}
