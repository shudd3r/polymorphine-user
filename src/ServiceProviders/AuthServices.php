<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\ServiceProviders;

use Polymorphine\User\Repository;
use Polymorphine\User\UserSession;
use Polymorphine\User\Authentication;
use Polymorphine\User\Authentication\CsrfTokenRefresh;
use Polymorphine\User\Authentication\EnableTokenOption;
use Polymorphine\User\Authentication\PasswordAuthentication;
use Polymorphine\User\Authentication\SessionAuthentication;
use Polymorphine\User\Authentication\TokenAuthentication;
use Polymorphine\User\Authentication\Token\PersistentCookieToken;
use Psr\Http\Server\MiddlewareInterface;


class AuthServices
{
    private $context;
    private $repository;

    private $userSession;
    private $cookieToken;

    public function __construct(SessionContextServices $context, Repository $repository)
    {
        $this->context    = $context;
        $this->repository = $repository;
    }

    public function setRememberCookie(string $name, array $directives = []): void
    {
        $tokenCookie = $this->context->responseHeaders()->cookieSetup()->directives($directives);
        $this->cookieToken = new PersistentCookieToken($tokenCookie->permanentCookie($name), $this->repository);
    }

    public function sessionAuthentication(): MiddlewareInterface
    {
        return $this->cookieToken
            ? new AuthMiddleware(new SessionAuthentication($this->userSession()), $this->tokenAuth())
            : new AuthMiddleware(new SessionAuthentication($this->userSession()));
    }

    public function passwordAuthentication(): MiddlewareInterface
    {
        $auth = $this->csrfResetWrapper(new PasswordAuthentication($this->userSession()));

        return $this->cookieToken
            ? new AuthMiddleware(new EnableTokenOption($auth, $this->cookieToken))
            : new AuthMiddleware($auth);
    }

    private function userSession(): UserSession
    {
        if ($this->userSession) { return $this->userSession; }
        return $this->userSession = new UserSession($this->context->sessionData(), $this->repository);
    }

    private function tokenAuth(): Authentication
    {
        return $this->csrfResetWrapper(new TokenAuthentication($this->userSession(), $this->cookieToken));
    }

    private function csrfResetWrapper(Authentication $auth): Authentication
    {
        return new CsrfTokenRefresh($auth, $this->context->csrfContext());
    }
}
