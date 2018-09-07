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
use Polymorphine\User\Data\Credentials;
use Polymorphine\Http\Context\Response\ResponseHeaders;
use Psr\Http\Message\ServerRequestInterface;


class PasswordAuthentication extends AuthMiddleware
{
    private $headers;
    private $userSession;
    private $token;

    public function __construct(ResponseHeaders $headers, UserSession $userSession) {
        $this->headers     = $headers;
        $this->userSession = $userSession;
    }

    protected function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getMethod() !== 'POST') { return $request; }

        $credentials = $this->credentials($request->getParsedBody());
        if (!$credentials) { return $request; }

        if (!$id = $this->userSession->signIn($credentials)) {
            $this->headers->cookie(UserSession::REMEMBER_COOKIE)->remove();
            return $request;
        }

        if ($this->token) {
            $this->headers->cookie(UserSession::REMEMBER_COOKIE)->httpOnly()->permanent()->value($this->token);
        }

        return $request->withAttribute(static::USER_ATTR, $id);
    }

    private function credentials(array $data): ?Credentials
    {
        $login      = $data[UserSession::USER_LOGIN_FIELD] ?? null;
        $password   = $data[UserSession::USER_PASS_FIELD] ?? null;
        $persistent = $data[UserSession::REMEMBER_COOKIE] ?? null;
        if (!$login || !$password) { return null; }

        if ($persistent) {
            $tokenKey  = uniqid();
            $tokenHash = bin2hex(random_bytes(32));
            $this->token = $tokenKey . UserSession::TOKEN_SEPARATOR . $tokenHash;
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
