<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Authentication;

use Polymorphine\User\Authentication;
use Polymorphine\User\Cookies\ResponseCookies;
use Polymorphine\User\Repository;
use Polymorphine\User\UserEntity;


class CookieAuthentication implements Authentication
{
    protected const REMEMBER_COOKIE = 'remember';

    private $cookies;
    private $repository;
    private $token;

    public function __construct(ResponseCookies $cookies, Repository $repository)
    {
        $this->cookies    = $cookies;
        $this->repository = $repository;
    }

    public function credentials(array $credentials): void
    {
        $this->token = $credentials[self::REMEMBER_COOKIE] ?? null;
    }

    public function user(): UserEntity
    {
        $user = ($this->token)
            ? $this->repository->getUserByCookieToken($this->token)
            : $this->repository->guestUser();

        if (!$user->isLoggedIn() && $this->token) {
            $this->cookies->cookie(self::REMEMBER_COOKIE)->remove();
        }

        return $user;
    }
}
