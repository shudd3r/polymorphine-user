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
    protected const SESSION_USER_ID = 'id';

    private $session;
    private $users;
    private $rememberToken;

    public function __construct(User\Session $session, User\Repository $users)
    {
        $this->session = $session;
        $this->users   = $users;
    }

    public function credentials(ServerRequestInterface $request): void
    {
        $cookies = $request->getCookieParams();

        $this->rememberToken = $cookies['remember'] ?? null;
    }

    public function user(): User\UserEntity
    {
        if ($id = $this->session->get(self::SESSION_USER_ID)) {
            return $this->users->getUserById($id);
        }

        if ($this->rememberToken) {
            $user = $this->users->getUserByCookieToken($this->rememberToken);
            $this->session->set(self::SESSION_USER_ID, $user->id());
        }

        return $this->users->guestUser();
    }
}
