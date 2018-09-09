<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\AuthMiddleware;

use Polymorphine\User\AuthMiddleware;
use Polymorphine\User\UserSession;
use Polymorphine\User\PersistentAuthCookie;
use Psr\Http\Message\ServerRequestInterface;


class CookieAuthentication extends AuthMiddleware
{
    private $userSession;
    private $authCookie;

    public function __construct(UserSession $userSession, PersistentAuthCookie $authCookie)
    {
        $this->userSession = $userSession;
        $this->authCookie  = $authCookie;
    }

    protected function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $cookies = $request->getCookieParams();
        if (!$credentials = $this->authCookie->credentials($cookies)) { return $request; }

        if (!$id = $this->userSession->signIn($credentials)) {
            $this->authCookie->clear();
        }

        return $request->withAttribute(static::USER_ATTR, $id);
    }
}
