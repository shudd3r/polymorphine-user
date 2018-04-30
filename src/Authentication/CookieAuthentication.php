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
use Polymorphine\User\Repository;
use Polymorphine\User\Cookies\CookieJar;
use Polymorphine\User\UserEntity;
use Psr\Http\Message\ServerRequestInterface;


class CookieAuthentication implements Authentication
{
    protected const REMEMBER_COOKIE = 'remember';

    private $cookies;
    private $repository;
    private $user;

    public function __construct(CookieJar $cookies, Repository $repository)
    {
        $this->cookies    = $cookies;
        $this->repository = $repository;
    }

    public function authenticate(ServerRequestInterface $request): void
    {
        if ($this->user) { return; }

        $cookieToken = $this->cookies->getValue(self::REMEMBER_COOKIE);
        $this->user  = ($cookieToken)
            ? $this->repository->getUserByCookieToken($cookieToken)
            : $this->repository->guestUser();

        if (!$this->user->isLoggedIn()) {
            $this->cookies->clear(self::REMEMBER_COOKIE);
        }
    }

    public function user(): UserEntity
    {
        return $this->user ?? $this->user = $this->repository->guestUser();
    }
}
