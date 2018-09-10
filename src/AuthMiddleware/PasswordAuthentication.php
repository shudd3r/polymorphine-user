<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\AuthMiddleware;

use Polymorphine\User\AuthMiddleware;
use Polymorphine\User\UserSession;
use Polymorphine\User\PersistentAuthCookie;
use Polymorphine\User\Data\Credentials;
use Psr\Http\Message\ServerRequestInterface;


class PasswordAuthentication extends AuthMiddleware
{
    public const USER_LOGIN_FIELD = 'username';
    public const USER_PASS_FIELD  = 'password';
    public const REMEMBER_FIELD   = 'remember';

    private $userSession;
    private $authCookie;

    public function __construct(UserSession $userSession, PersistentAuthCookie $authCookie = null)
    {
        $this->userSession = $userSession;
        $this->authCookie  = $authCookie;
    }

    protected function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getMethod() !== 'POST') { return $request; }

        $payload     = $request->getParsedBody();
        $credentials = $this->credentials($payload);
        if (!$credentials) { return $request; }

        if (!$this->userSession->signIn($credentials)) { return $request; }

        if ($this->authCookie && isset($payload[static::REMEMBER_FIELD])) {
            $this->authCookie->setToken($this->userSession->user()->id());
        }

        return $request->withAttribute(static::AUTH_ATTR, true);
    }

    private function credentials(array $data): ?Credentials
    {
        $login    = $data[static::USER_LOGIN_FIELD] ?? null;
        $password = $data[static::USER_PASS_FIELD] ?? null;

        if (!$login || !$password) { return null; }

        $emailLogin = strpos($login, '@');
        return new Credentials([
            'name'     => $emailLogin ? null : $login,
            'email'    => $emailLogin ? $login : null,
            'password' => $password
        ]);
    }
}
