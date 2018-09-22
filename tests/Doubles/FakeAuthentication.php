<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Doubles;

use Polymorphine\User\Authentication;
use Polymorphine\User\AuthenticatedUser;
use Psr\Http\Message\ServerRequestInterface;


class FakeAuthentication implements Authentication
{
    private $success;

    public function __construct(bool $success = true)
    {
        $this->success = $success;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        return $this->success ? new FakeAuthUser(1) : new FakeAuthUser(0);
    }
}
