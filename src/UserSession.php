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

use Polymorphine\Http\Context\Session;
use Polymorphine\User\Data\Credentials;


class UserSession
{
    public const SESSION_USER_KEY = 'userId';

    private $repository;
    private $session;

    private $authenticatedUser;

    public function __construct(Session $session, Repository $repository)
    {
        $this->session    = $session;
        $this->repository = $repository;
    }

    public function user(): AuthenticatedUser
    {
        return $this->authenticatedUser ?? $this->authenticatedUser = $this->repository->getAnonymousUser();
    }

    public function resume(): bool
    {
        $id = $this->session->data()->get(static::SESSION_USER_KEY);
        if (!$id) { return false; }

        $user = $this->repository->getUser(new Credentials(['id' => $id]));
        if (!$user->isLoggedIn()) {
            $this->session->data()->clear();
            return false;
        }

        $this->authenticatedUser = $user;
        return true;
    }

    public function signIn(Credentials $credentials): bool
    {
        $user = $this->repository->getUser($credentials);
        if (!$user->isLoggedIn()) { return false; }

        $this->session->resetContext();
        $this->session->data()->set(static::SESSION_USER_KEY, $user->id());

        $this->authenticatedUser = $user;
        return true;
    }
}
