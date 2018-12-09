<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Services;

use Polymorphine\User\Repository;
use Polymorphine\Headers\Cookie;
use Polymorphine\User\UserSession;
use Polymorphine\User\Authentication;
use Polymorphine\User\Authentication\CsrfTokenRefresh;
use Polymorphine\User\Authentication\EnableTokenOption;
use Polymorphine\User\Authentication\PasswordAuthentication;
use Polymorphine\User\Authentication\SessionAuthentication;
use Polymorphine\User\Authentication\TokenAuthentication;
use Polymorphine\User\Authentication\Token\PersistentCookieToken;
use Polymorphine\Middleware\MiddlewareChain;
use Polymorphine\Middleware\LazyMiddleware;
use Psr\Http\Server\MiddlewareInterface;


class AuthServices
{
    private $context;
    private $repository;
    private $cookieToken;

    private $userSession;

    public function __construct(ProcessContextServices $context, Repository $repository, Cookie $cookie = null)
    {
        $this->context     = $context;
        $this->repository  = $repository;
        $this->cookieToken = !$cookie ?: new PersistentCookieToken($cookie, $repository);
    }

    public function sessionAuthentication(): MiddlewareInterface
    {
        return new MiddlewareChain(
            $this->context->responseHeaders(),
            $this->context->sessionContext(),
            new LazyMiddleware(function () {
                return new MiddlewareChain(
                    $this->context->csrfContext(),
                    $this->cookieToken
                        ? new AuthMiddleware(new SessionAuthentication($this->userSession()), $this->tokenAuth())
                        : new AuthMiddleware(new SessionAuthentication($this->userSession()))
                );
            })
        );
    }

    public function passwordAuthentication(): MiddlewareInterface
    {
        return new LazyMiddleware(function () {
            $auth = $this->csrfResetWrapper(new PasswordAuthentication($this->userSession()));

            return $this->cookieToken
                ? new AuthMiddleware(new EnableTokenOption($auth, $this->cookieToken))
                : new AuthMiddleware($auth);
        });
    }

    public function signOutMiddleware(): MiddlewareInterface
    {
        return new SignOutMiddleware($this->userSession(), $this->cookieToken);
    }

    public function user()
    {
        return $this->userSession()->user();
    }

    private function userSession(): UserSession
    {
        return $this->userSession
            ?: $this->userSession = new UserSession($this->context->sessionContext()->data(), $this->repository);
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
