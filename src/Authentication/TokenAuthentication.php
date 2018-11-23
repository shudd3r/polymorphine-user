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
use Polymorphine\User\UserSession;
use Polymorphine\User\AuthenticatedUser;
use Psr\Http\Message\ServerRequestInterface;


class TokenAuthentication implements Authentication
{
    private $userSession;
    private $token;

    public function __construct(UserSession $userSession, Token $token)
    {
        $this->userSession = $userSession;
        $this->token       = $token;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $credentials = $this->token->credentials($request);
        if (!$credentials) { return $this->userSession->user(); }

        $user = $this->userSession->signIn($credentials);
        if (!$user->isLoggedIn()) { $this->token->revoke(); }

        return $user;
    }
}
