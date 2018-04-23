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

use Polymorphine\User;


class CookieAuthentication implements User\Authentication
{
    protected const REMEMBER_COOKIE = 'remember';

    private $session;
    private $repository;
    private $user;

    public function __construct(User\Session $session, User\Repository $repository)
    {
        $this->session    = $session;
        $this->repository = $repository;
    }

    public function authenticate(array $credentials): void
    {
        if ($this->user) { return; }

        $cookieToken = $credentials[self::REMEMBER_COOKIE] ?? null;
        $this->user  = ($cookieToken)
            ? $this->repository->getUserByCookieToken($cookieToken)
            : $this->repository->guestUser();

        if ($this->user->isLoggedIn()) {
            $this->session->set($this->session::USER_ID_KEY, $this->user->id());
        } elseif ($cookieToken) {
            //TODO: remove invalid cookie
        }
    }

    public function user(): User\UserEntity
    {
        return $this->user ?? $this->user = $this->repository->guestUser();
    }
}
