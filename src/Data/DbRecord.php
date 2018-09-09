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
use Polymorphine\User\Repository;


class DbRecord extends Data
{
    private $repository;
    private $password;
    private $token;

    public function __construct(array $data, Repository $repository)
    {
        parent::__construct($data);

        $this->repository = $repository;
        $this->password   = (string) $this->pullFromData('password');
        $this->token      = (string) $this->pullFromData('token');
    }

    public function verifyPassword(string $password): bool
    {
        if (!password_verify($password, $this->password)) { return false; }
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->repository->setPassword($this->id, password_hash($password, PASSWORD_DEFAULT));
        }
        return true;
    }

    public function verifyToken(string $token): bool
    {
        return hash_equals($this->token, hash('sha256', $token));
    }
}
