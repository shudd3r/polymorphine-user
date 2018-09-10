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

use Polymorphine\Http\Context\SessionManager;
use Polymorphine\User\Data\Credentials;


class UserSession
{
    public const SESSION_USER_KEY = 'userId';

    private $repository;
    private $sessionManager;

    private $authenticatedUser;

    public function __construct(SessionManager $sessionManager, Repository $repository)
    {
        $this->sessionManager = $sessionManager;
        $this->repository     = $repository;
    }

    public function user(): AuthenticatedUser
    {
        return $this->authenticatedUser ?? $this->authenticatedUser = $this->repository->getAnonymousUser();
    }

    public function resume(): bool
    {
        $id = $this->sessionManager->session()->get(static::SESSION_USER_KEY);
        if (!$id) { return false; }

        $user = $this->repository->getUser(new Credentials(['id' => $id]));
        if (!$user->isLoggedIn()) {
            $this->sessionManager->session()->clear();
            return false;
        }

        $this->authenticatedUser = $user;
        return true;
    }

    public function signIn(Credentials $credentials): bool
    {
        $user = $this->repository->getUser($credentials);
        if (!$user->isLoggedIn()) { return false; }

        $this->sessionManager->session()->set(static::SESSION_USER_KEY, $user->id());
        $this->sessionManager->regenerateId();

        $this->authenticatedUser = $user;
        return true;
    }
}
