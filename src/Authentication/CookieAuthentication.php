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
use Polymorphine\User\AuthenticatedUser;
use Polymorphine\User\UserSession;
use Polymorphine\User\PersistentAuthCookie;
use Psr\Http\Message\ServerRequestInterface;


class CookieAuthentication implements Authentication
{
    private $userSession;
    private $authCookie;

    public function __construct(UserSession $userSession, PersistentAuthCookie $authCookie)
    {
        $this->userSession = $userSession;
        $this->authCookie  = $authCookie;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $credentials = $this->authCookie->credentials($request->getCookieParams());
        if (!$credentials) { return $this->userSession->user(); }

        $user = $this->userSession->signIn($credentials);
        if (!$user->isLoggedIn()) {
            $this->authCookie->clear();
        }

        return $user;
    }
}
