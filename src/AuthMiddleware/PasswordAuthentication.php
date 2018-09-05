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
use Polymorphine\User\Authentication;
use Polymorphine\User\Data\Credentials;
use Polymorphine\Http\Context\SessionManager;
use Polymorphine\Http\Context\Response\ResponseHeaders;
use Psr\Http\Message\ServerRequestInterface;


class PasswordAuthentication extends AuthMiddleware
{
    private $headers;
    private $sessionManager;
    private $auth;
    private $cookie;

    public function __construct(
        ResponseHeaders $headers,
        SessionManager $sessionManager,
        Authentication $auth
    ) {
        $this->headers        = $headers;
        $this->sessionManager = $sessionManager;
        $this->auth           = $auth;
    }

    protected function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getMethod() !== 'POST') { return $request; }

        $credentials = $this->credentials($request->getParsedBody());
        if (!$credentials) { return $request; }

        $user = $this->auth->signIn($credentials);
        if (!$user->isLoggedIn()) {
            $this->headers->cookie($this->auth::REMEMBER_COOKIE)->remove();
            return $request;
        }

        if ($this->cookie) {
            $this->headers->cookie($this->auth::REMEMBER_COOKIE)
                          ->httpOnly()
                          ->permanent()
                          ->value($this->cookie);
        }

        $id = $user->id();
        $this->sessionManager->session()->set($this->auth::SESSION_USER_KEY, $id);
        $this->sessionManager->regenerateId();

        return $request->withAttribute(static::USER_ATTR, $id);
    }

    private function credentials(array $data): ?Credentials
    {
        $login      = $data[$this->auth::USER_LOGIN_FIELD] ?? null;
        $password   = $data[$this->auth::USER_PASS_FIELD] ?? null;
        $persistent = $data[$this->auth::REMEMBER_COOKIE] ?? null;
        if (!$login || !$password) { return null; }

        if ($persistent) {
            $tokenKey  = uniqid();
            $tokenHash = bin2hex(random_bytes(32));
            $this->cookie = $tokenKey . $this->auth::TOKEN_SEPARATOR . $tokenHash;
        }

        $emailLogin = strpos($login, '@');
        return new Credentials([
            'name'     => $emailLogin ? null : $login,
            'email'    => $emailLogin ? $login : null,
            'password' => $password,
            'tokenKey' => $tokenKey ?? null,
            'token'    => $tokenHash ?? null
        ]);
    }
}
