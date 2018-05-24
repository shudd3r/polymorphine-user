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
    public $tokenKey;

    private $password;
    private $passwordHash;

    private $token;
    private $tokenHash;

    public function __construct(array $data = [])
    {
        $this->id       = $data['id'] ?? null;
        $this->name     = $data['name'] ?? null;
        $this->email    = $data['email'] ?? null;
        $this->tokenKey = $data['tokenKey'] ?? null;

        $this->password     = $data['password'] ?? null;
        $this->passwordHash = $data['passwordHash'] ?? null;

        $this->token     = $data['token'] ?? null;
        $this->tokenHash = $data['tokenHash'] ?? null;
    }

    public function passwordMatch(UserData $user): bool
    {
        return $user->verifyPassword($this->password);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function tokenMatch(UserData $user): bool
    {
        return $user->verifyToken($this->token);
    }

    public function verifyToken(string $token): bool
    {
        return hash('sha256', $token) === $this->tokenHash;
    }
}
