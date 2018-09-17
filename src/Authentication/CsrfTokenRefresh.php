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
use Polymorphine\Http\Context\CsrfProtection\CsrfPersistentTokenContext;
use Psr\Http\Message\ServerRequestInterface;


class CsrfTokenRefresh implements Authentication
{
    private $tokenContext;
    private $authentication;

    public function __construct(Authentication $authentication, CsrfPersistentTokenContext $tokenContext)
    {
        $this->authentication = $authentication;
        $this->tokenContext   = $tokenContext;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $user = $this->authentication->authenticate($request);
        if ($user->isLoggedIn()) {
            $this->tokenContext->resetToken();
        }

        return $user;
    }
}
