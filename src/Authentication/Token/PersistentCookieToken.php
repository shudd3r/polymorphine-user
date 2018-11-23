<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Authentication\Token;

use Polymorphine\User\Authentication\Token;
use Polymorphine\Headers\Cookie;
use Polymorphine\User\Repository;
use Polymorphine\User\Data\Credentials;
use Psr\Http\Message\ServerRequestInterface;


class PersistentCookieToken implements Token
{
    public const TOKEN_SEPARATOR = ':';

    private $cookie;
    private $repository;

    public function __construct(Cookie $cookie, Repository $repository)
    {
        $this->cookie     = $cookie;
        $this->repository = $repository;
    }

    public function enableForUser($userId): void
    {
        $key   = uniqid();
        $token = bin2hex(random_bytes(32));

        $this->repository->setToken($userId, $key, hash('sha256', $token));
        $this->cookie->send($key . static::TOKEN_SEPARATOR . $token);
    }

    public function credentials(ServerRequestInterface $request): ?Credentials
    {
        $cookies = $request->getCookieParams();
        $token   = $cookies[$this->cookie->name()] ?? null;
        if (!$token) { return null; }

        [$key, $hash] = explode(static::TOKEN_SEPARATOR, $token) + [false, false];
        if (!$key || !$hash) {
            $this->revoke();
            return null;
        }

        return new Credentials([
            'tokenKey' => $key,
            'token'    => $hash
        ]);
    }

    public function revoke(): void
    {
        $this->cookie->revoke();
    }
}
