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
use Polymorphine\User\UserEntity;


class CompositeAuthentication implements Authentication
{
    private $authMethods;
    private $credentials;
    private $user;

    public function __construct(Authentication ...$authentications)
    {
        $this->authMethods = $authentications;
    }

    public function credentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }

    public function user(): UserEntity
    {
        if ($this->user) { return $this->user; }

        foreach ($this->authMethods as $auth) {
            $auth->credentials($this->credentials);
            $this->user = $auth->user();
            if ($this->user->isLoggedIn()) { break; }
        }

        return $this->user;
    }
}
