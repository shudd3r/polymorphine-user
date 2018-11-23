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
use Psr\Http\Message\ServerRequestInterface;


class EnableTokenOption implements Authentication
{
    public const REMEMBER_FIELD = 'remember';

    private $auth;
    private $token;

    public function __construct(Authentication $auth, Token $token)
    {
        $this->auth  = $auth;
        $this->token = $token;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $user = $this->auth->authenticate($request);
        if (!$user->isLoggedIn()) { return $user; }

        $payload = $request->getParsedBody();
        if (isset($payload[static::REMEMBER_FIELD])) {
            $this->token->enableForUser($user->id());
        }

        return $user;
    }
}
