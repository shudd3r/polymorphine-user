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

use Polymorphine\User;


class CompositeAuthentication implements User\Authentication
{
    private $authentications;
    private $user;

    public function __construct(User\Authentication ...$authentications)
    {
        $this->authentications = $authentications;
    }

    public function authenticate(array $credentials): void
    {
        foreach ($this->authentications as $auth) {
            $auth->authenticate($credentials);
            $this->user = $auth->user();
            if ($this->user->isLoggedIn()) { break; }
        }
    }

    public function user(): User\UserEntity
    {
        return $this->user;
    }
}
