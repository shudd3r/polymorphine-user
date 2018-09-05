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


class CookieAuthentication extends AuthMiddleware
{
    private $headers;
    private $sessionManager;
    private $auth;

    public function __construct(
        ResponseHeaders $headers,
        SessionManager $sessionManager,
        Authentication $auth
    ) {
        $this->headers        = $headers;
        $this->sessionManager = $sessionManager;
        $this->auth           = $auth;
    }

    public function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $cookies = $request->getCookieParams();
        if (!$credentials = $this->credentials($cookies)) { return $request; }

        $user = $this->auth->signIn($credentials);
        if (!$user->isLoggedIn()) {
            $this->headers->cookie($this->auth::REMEMBER_COOKIE)->remove();
            return $request;
        }

        $id = $user->id();
        $this->sessionManager->session()->set($this->auth::SESSION_USER_KEY, $id);
        $this->sessionManager->regenerateId();

        return $request->withAttribute(static::USER_ATTR, $id);
    }

    protected function credentials(array $cookies): ?Credentials
    {
        $token = $cookies[$this->auth::REMEMBER_COOKIE] ?? null;
        if (!$token) { return null; }

        [$key, $hash] = explode($this->auth::TOKEN_SEPARATOR, $token);
        if (!$key || !$hash) { return null; }

        return new Credentials([
            'tokenKey' => $key,
            'token'    => $hash
        ]);
    }
}
