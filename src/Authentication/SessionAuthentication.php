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
use Psr\Http\Message\ServerRequestInterface;


class SessionAuthentication implements Authentication
{
    private $userSession;

    public function __construct(UserSession $userSession)
    {
        $this->userSession = $userSession;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        return $this->userSession->resume();
    }
}
