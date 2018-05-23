<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User;


class UserData
{
    public $id;
    public $name;
    public $email;
    public $passwordHash;
    public $tokenKey;
    public $tokenHash;

    public function __construct(array $data = [])
    {
        $this->id           = $data['id'] ?? null;
        $this->name         = $data['username'] ?? null;
        $this->email        = $data['email'] ?? null;
        $this->passwordHash = $data['passwordHash'] ?? null;
        $this->tokenKey     = $data['tokenKey'] ?? null;
        $this->tokenHash    = $data['tokenHash'] ?? null;
    }
}
