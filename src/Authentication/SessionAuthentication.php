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
    private $users;
    private $authenticatedUser;

    public function __construct(User\Session $session, User\Repository $users)
    {
        $this->session = $session;
        $this->users   = $users;
    }

    public function credentials(array $tokens): void
    {
        if ($this->authenticatedUser) { return; }

        $user = null;
        if (isset($tokens['session']) && $id = $this->session->get(self::USER_ID_KEY)) {
            $user = $this->users->getUserById($id);
        } elseif (isset($tokens['remember'])) {
            $user = $this->users->getUserByCookieToken($tokens['remember']);
            $this->persistSession($user);
        }

        $this->authenticate($user ?: $this->users->guestUser());
    }

    public function user(): User\UserEntity
    {
        return $this->authenticatedUser ?? $this->authenticatedUser = $this->users->guestUser();
    }

    private function authenticate(User\UserEntity $user)
    {
        if (!$user->isLoggedIn()) { $this->clearCredentials(); }

        $this->authenticatedUser = $user;
    }

    private function persistSession(User\UserEntity $user)
    {
        $this->session->set(self::USER_ID_KEY, $user->id());
    }

    private function clearCredentials()
    {
        $this->session->clear(self::USER_ID_KEY);
    }
}
