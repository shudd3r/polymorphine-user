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


class Authentication
{
    public const SESSION_USER_KEY = 'userId';
    public const REMEMBER_COOKIE  = 'remember';
    public const TOKEN_SEPARATOR  = ':';

    private $repository;
    private $user;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function signIn(Credentials $credentials): AuthenticatedUser
    {
        return $this->user ?? $this->user = $this->repository->getUser($credentials);
    }

    public function user(): AuthenticatedUser
    {
        return $this->user ?? $this->user = $this->repository->getAnonymousUser();
    }
}
