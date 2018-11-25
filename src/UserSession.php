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

use Polymorphine\Session\SessionContext\SessionData;
use Polymorphine\User\Data\Credentials;


class UserSession
{
    public const SESSION_USER_KEY = 'userId';

    private $repository;
    private $session;

    private $authenticatedUser;

    public function __construct(SessionData $session, Repository $repository)
    {
        $this->session    = $session;
        $this->repository = $repository;
    }

    public function user(): AuthenticatedUser
    {
        return $this->authenticatedUser ?? $this->authenticatedUser = $this->repository->getAnonymousUser();
    }

    public function resume(): AuthenticatedUser
    {
        $id = $this->session->get(static::SESSION_USER_KEY);
        if (!$id) { return $this->user(); }

        $this->authenticatedUser = $this->repository->getUser(new Credentials(['id' => $id]));
        if (!$this->authenticatedUser->isLoggedIn()) {
            $this->session->clear();
        }

        return $this->authenticatedUser;
    }

    public function signIn(Credentials $credentials): AuthenticatedUser
    {
        $this->authenticatedUser = $this->repository->getUser($credentials);
        if ($this->authenticatedUser->isLoggedIn()) {
            $this->session->resetContext();
            $this->session->set(static::SESSION_USER_KEY, $this->authenticatedUser->id());
        }

        return $this->authenticatedUser;
    }
}
