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
use Polymorphine\App\Context\CsrfProtection;
use Psr\Http\Message\ServerRequestInterface;


class CsrfTokenRefresh implements Authentication
{
    private $csrfProtection;
    private $authentication;

    public function __construct(Authentication $authentication, CsrfProtection $csrfProtection)
    {
        $this->authentication = $authentication;
        $this->csrfProtection = $csrfProtection;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $user = $this->authentication->authenticate($request);
        if ($user->isLoggedIn()) {
            $this->csrfProtection->resetToken();
        }

        return $user;
    }
}
