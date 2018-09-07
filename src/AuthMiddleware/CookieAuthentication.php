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


class CookieAuthentication extends AuthMiddleware
{
    private $headers;
    private $userSession;

    public function __construct(ResponseHeaders $headers, UserSession $userSession) {
        $this->headers     = $headers;
        $this->userSession = $userSession;
    }

    public function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $cookies = $request->getCookieParams();
        if (!$credentials = $this->credentials($cookies)) { return $request; }

        if (!$id = $this->userSession->signIn($credentials)) {
            $this->headers->cookie(UserSession::REMEMBER_COOKIE)->remove();
            return $request;
        }

        return $request->withAttribute(static::USER_ATTR, $id);
    }

    protected function credentials(array $cookies): ?Credentials
    {
        $token = $cookies[UserSession::REMEMBER_COOKIE] ?? null;
        if (!$token) { return null; }

        [$key, $hash] = explode(UserSession::TOKEN_SEPARATOR, $token);
        if (!$key || !$hash) { return null; }

        return new Credentials([
            'tokenKey' => $key,
            'token'    => $hash
        ]);
    }
}
