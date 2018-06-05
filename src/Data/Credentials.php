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


class Credentials extends Data
{
    private $password;
    private $token;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->password = (string) $this->pullFromData('password');
        $this->token    = (string) $this->pullFromData('token');
    }

    public function match(DbRecord $dbUser): bool
    {
        if (!$this->password && !$this->token) { return false; }

        return $this->password
            ? $this->verifyPassword($dbUser)
            : $dbUser->verifyToken($this->token);
    }

    private function verifyPassword(DbRecord $dbUser): bool
    {
        $isValid = $dbUser->verifyPassword($this->password);
        if (!$isValid) { return false; }

        if ($this->tokenKey && $this->token) {
            $dbUser->resetToken($this->tokenKey, $this->token);
        }
        return true;
    }
}
