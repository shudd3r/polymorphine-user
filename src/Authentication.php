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

    public function authenticate(UserData $user): ?int
    {
        if ($this->user) { return $this->user->id(); }

        if ($user->id) {
            $dbUser = $this->database->match($user);
            return $dbUser ? $this->createUser($dbUser) : null;
        }

        if ($user->tokenKey) {
            return $this->authenticateWithToken($user);
        }

        if ($user->name || $user->email) {
            return $this->authenticateWithPassword($user);
        }

        return null;
    }

    public function user(): AuthenticatedUser
    {
        return $this->user ?? $this->user = $this->factory->anonymous();
    }

    protected function createUser(UserData $user): int
    {
        $this->user = $this->factory->create($user);
        return $user->id;
    }

    private function authenticateWithPassword(UserData $user): ?int
    {
        $dbUser = $this->database->match($user);
        return ($dbUser && $user->passwordMatch($dbUser)) ? $this->createUser($dbUser) : null;
    }

    private function authenticateWithToken(UserData $user): ?int
    {
        $dbUser = $this->database->match($user);
        return ($dbUser && $user->tokenMatch($dbUser)) ? $this->createUser($dbUser) : null;
    }
}
