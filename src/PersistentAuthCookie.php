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

use Polymorphine\Headers\Cookie;
use Polymorphine\User\Data\Credentials;


class PersistentAuthCookie
{
    public const TOKEN_SEPARATOR = ':';

    private $cookie;
    private $repository;

    public function __construct(Cookie $cookie, Repository $repository)
    {
        $this->cookie     = $cookie;
        $this->repository = $repository;
    }

    public function setToken($id): void
    {
        $key   = uniqid();
        $token = bin2hex(random_bytes(32));

        $this->repository->setToken($id, $key, hash('sha256', $token));
        $this->cookie->send($key . static::TOKEN_SEPARATOR . $token);
    }

    public function credentials(array $cookies): ?Credentials
    {
        $token = $cookies[$this->cookie->name()] ?? null;
        if (!$token) { return null; }

        [$key, $hash] = explode(static::TOKEN_SEPARATOR, $token) + [false, false];
        if (!$key || !$hash) {
            $this->clear();
            return null;
        }

        return new Credentials([
            'tokenKey' => $key,
            'token'    => $hash
        ]);
    }

    public function clear(): void
    {
        $this->cookie->revoke();
    }
}
