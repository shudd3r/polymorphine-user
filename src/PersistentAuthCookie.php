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

use Polymorphine\App\Context\ResponseHeaders;
use Polymorphine\User\Data\Credentials;


class PersistentAuthCookie
{
    public const COOKIE_NAME     = 'remember';
    public const TOKEN_SEPARATOR = ':';

    private $headers;
    private $repository;
    private $cookieOptions;

    public function __construct(ResponseHeaders $headers, Repository $repository, array $cookieOptions = [])
    {
        $this->headers       = $headers;
        $this->repository    = $repository;
        $this->cookieOptions = $cookieOptions;
    }

    public function setToken($id): void
    {
        $key   = uniqid();
        $token = bin2hex(random_bytes(32));

        $this->repository->setToken($id, $key, hash('sha256', $token));

        $attributes = $this->cookieOptions + ['httpOnly' => true, 'sameSite' => 'Lax', 'expires' => false];
        $this->headers->cookie(static::COOKIE_NAME, $attributes)
                      ->value($key . static::TOKEN_SEPARATOR . $token);
    }

    public function credentials(array $cookies): ?Credentials
    {
        $token = $cookies[static::COOKIE_NAME] ?? null;
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
        $this->headers->cookie(static::COOKIE_NAME)->remove();
    }
}
