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
use Polymorphine\Http\Server\Response\ResponseHeaders;
use Polymorphine\Http\Server\Session\SessionStorage;
use Psr\Http\Message\ServerRequestInterface;
use Polymorphine\User\Data\Credentials;


class CookieAuthentication extends AuthMiddleware
{
    protected const TOKEN_SEPARATOR = ':';

    private $headers;
    private $session;
    private $auth;

    public function __construct(ResponseHeaders $headers, SessionStorage $session, Authentication $auth)
    {
        $this->headers = $headers;
        $this->session = $session;
        $this->auth    = $auth;
    }

    public function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $cookies = $request->getCookieParams();
        $token   = $cookies[Authentication::REMEMBER_COOKIE] ?? null;
        if (!$token) { return $request; }

        $user = $this->auth->signIn($this->credentials($token));
        if (!$user->isLoggedIn()) {
            $this->headers->cookie(Authentication::REMEMBER_COOKIE)->remove();
            return $request;
        }

        $id = $user->id();
        $this->session->set(Authentication::SESSION_USER_KEY, $id);

        return $request->withAttribute(static::USER_ATTR, $id);
    }

    protected function credentials(string $cookieToken): Credentials
    {
        [$key, $hash] = explode(static::TOKEN_SEPARATOR, $cookieToken);

        return new Credentials([
            'tokenKey' => $key,
            'token'    => $hash
        ]);
    }
}
