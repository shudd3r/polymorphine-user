<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Data;

use Polymorphine\User\Data;


class DbRecord extends Data
{
    private $password;
    private $token;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->password = (string) $this->pullFromData('password');
        $this->token    = (string) $this->pullFromData('token');
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function verifyToken(string $token): bool
    {
        //TODO: clear token hash in db when doesn't match (tokenKey was guessed)
        return hash_equals($this->token, hash('sha256', $token));
    }
}
