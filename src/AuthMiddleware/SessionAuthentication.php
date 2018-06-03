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
use Polymorphine\User\Authentication;
use Polymorphine\Http\Context\Session;
use Psr\Http\Message\ServerRequestInterface;
use Polymorphine\User\Data\Credentials;


class SessionAuthentication extends AuthMiddleware
{
    private $session;
    private $auth;

    public function __construct(Session $session, Authentication $auth)
    {
        $this->session = $session;
        $this->auth    = $auth;
    }

    protected function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $id = $this->session->get(Authentication::SESSION_USER_KEY);
        if (!$id) { return $request; }

        $user = $this->auth->signIn(new Credentials(['id' => $id]));
        if (!$user->isLoggedIn()) {
            $this->session->clear();
            return $request;
        }

        return $request->withAttribute(static::USER_ATTR, $id);
    }
}
