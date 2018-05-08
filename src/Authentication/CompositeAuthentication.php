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


class CompositeAuthentication implements Authentication
{
    private $authMethods;

    public function __construct(Authentication ...$authentications)
    {
        $this->authMethods = $authentications;
    }

    public function authenticate(array $credentials): ?int
    {
        foreach ($this->authMethods as $auth) {
            if ($id  = $auth->authenticate($credentials)) { break; }
        }

        return $id ?? null;
    }
}
