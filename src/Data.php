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


class Data
{
    public $id;
    public $name;
    public $email;
    public $tokenKey;

    public function __construct(array $data = [])
    {
        $this->id       = $data['id'] ?? null;
        $this->name     = $data['name'] ?? null;
        $this->email    = $data['email'] ?? null;
        $this->tokenKey = $data['tokenKey'] ?? null;
    }
}
