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
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class AuthMiddleware implements MiddlewareInterface
{
    private $authMethods;
    private $authQueue;

    public function __construct(Authentication ...$authMethods)
    {
        $this->authMethods = $authMethods;
        $this->authQueue   = $authMethods;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->getAttribute(Authentication::AUTH_ATTR)) {
            $request = $this->authenticatedRequest($request);
        }
        $this->authQueue = $this->authMethods;
        return $handler->handle($request);
    }

    private function authenticatedRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $authenticated = false;
        while (!$authenticated && $authMethod = array_shift($this->authQueue)) {
            $authenticated = $authMethod->authenticate($request)->isLoggedIn();
        }
        return $authenticated ? $request->withAttribute(Authentication::AUTH_ATTR, $authenticated) : $request;
    }
}
