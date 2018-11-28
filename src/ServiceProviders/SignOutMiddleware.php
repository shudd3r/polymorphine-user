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

use Polymorphine\User\Authentication;
use Polymorphine\User\Authentication\Token;
use Polymorphine\User\UserSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class SignOutMiddleware implements MiddlewareInterface
{
    private $session;
    private $token;

    public function __construct(UserSession $session, Token $token)
    {
        $this->session = $session;
        $this->token   = $token;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->signOut();
        $this->token->revoke();

        return $handler->handle($request->withoutAttribute(Authentication::AUTH_ATTR));
    }
}
