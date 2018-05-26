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

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


abstract class AuthMiddleware implements MiddlewareInterface
{
    public const USER_ATTR = 'userId';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $request->getAttribute(self::USER_ATTR)
            ? $handler->handle($request)
            : $handler->handle($this->authenticate($request));
    }

    abstract protected function authenticate(ServerRequestInterface $request): ServerRequestInterface;
}
