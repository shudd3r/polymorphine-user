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

use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Data\DbRecord;


class Authentication
{
    public const SESSION_USER_KEY = 'userId';
    public const REMEMBER_COOKIE  = 'remember';

    /** @var AuthenticatedUser */
    protected $user;

    private $database;
    private $factory;

    public function __construct(DataGateway $database, Factory $factory)
    {
        $this->database = $database;
        $this->factory  = $factory;
    }

    public function authenticate(Credentials $credentials): ?int
    {
        if ($this->user) { return $this->user->id(); }

        $dbUser = $this->database->match($credentials);
        if (!$dbUser) { return null; }

        return $credentials->id
            ? $this->createUser($dbUser)
            : $this->verified($dbUser, $credentials);
    }

    public function user(): AuthenticatedUser
    {
        return $this->user ?? $this->user = $this->factory->anonymous();
    }

    protected function createUser(Data $user): int
    {
        $this->user = $this->factory->create($user);
        return $user->id;
    }

    private function verified(DbRecord $user, Credentials $credentials): ?int
    {
        return $credentials->match($user)
            ? $this->createUser($user)
            : null;
    }
}
