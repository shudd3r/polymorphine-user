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


class SessionAuthentication implements User\Authentication
{
    protected const USER_ID_KEY = 'id';

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

        $userId = isset($credentials['session']) ? $this->session->get(self::USER_ID_KEY) : null;
        if (!$userId) {
            $this->user = $this->repository->guestUser();
            return;
        }

        $this->user = $this->repository->getUserById($userId);

        if (!$this->user->isLoggedIn()) {
            $this->session->clear(self::USER_ID_KEY);
        }
    }

    public function user(): User\UserEntity
    {
        return $this->user ?? $this->user = $this->repository->guestUser();
    }
}
