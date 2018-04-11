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
use Psr\Http\Message\ServerRequestInterface;


class SessionAuthentication implements User\Authentication
{
    protected const USER_ID_KEY = 'id';

    private $session;
    private $users;
    private $authenticatedUser;

    public function __construct(User\Session $session, User\Repository $users)
    {
        $this->session = $session;
        $this->users   = $users;
    }

    public function credentials(ServerRequestInterface $request): void
    {
        if ($this->authenticatedUser) { return; }

        if ($id = $this->session->get(self::USER_ID_KEY)) {
            $user = $this->users->getUserById($id);
            $this->persistSession($user);
            return;
        }

        $cookies = $request->getCookieParams();
        $token   = $cookies['remember'] ?? null;

        if ($token) {
            $user = $this->users->getUserByCookieToken($token);
            $this->persistSession($user);
            return;
        }

        $this->authenticatedUser = $this->users->guestUser();
    }

    public function user(): User\UserEntity
    {
        return $this->authenticatedUser ?? $this->authenticatedUser = $this->users->guestUser();
    }

    private function persistSession(User\UserEntity $user)
    {
        $this->authenticatedUser = $user;

        if (!$user->isLoggedIn()) {
            $this->clearCredentials();
        } else {
            $this->session->set(self::USER_ID_KEY, $user->id());
        }
    }

    private function clearCredentials()
    {
        $this->session->clear(self::USER_ID_KEY);
    }
}
