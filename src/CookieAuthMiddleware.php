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

use Polymorphine\User\Authentication\SessionAuthentication;
use Polymorphine\User\Session\ServerAPISession;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class CookieAuthMiddleware implements MiddlewareInterface
{
    protected $cookies;
    protected $users;

    public function __construct(Cookies $cookies, Repository $users)
    {
        $this->cookies = $cookies;
        $this->users   = $users;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $auth = new SessionAuthentication(new ServerAPISession($this->cookies), $this->users);

        $auth->authenticate();

        return $this->cookies->setHeaders($handler->handle($request));
    }
}
