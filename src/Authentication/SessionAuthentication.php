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
use Polymorphine\User\Session\SessionStorage;
use Polymorphine\User\Repository;
use Polymorphine\User\AuthenticatedUser;


class SessionAuthentication implements Authentication
{
    private $session;
    private $repository;

    public function __construct(SessionStorage $session, Repository $repository)
    {
        $this->session    = $session;
        $this->repository = $repository;
    }

    public function credentials(array $credentials): void
    {
    }

    public function user(): AuthenticatedUser
    {
        $userId = $this->session->get($this->session::USER_ID_KEY);
        if (!$userId) {
            return $this->repository->anonymousUser();
        }

        $user = $this->repository->getUserById($userId);

        if (!$user->isLoggedIn()) {
            $this->session->clear($this->session::USER_ID_KEY);
        }

        return $user;
    }
}
