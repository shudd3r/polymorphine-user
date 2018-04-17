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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


abstract class AuthMiddleware implements MiddlewareInterface
{
    protected $session;

    public function __construct(Authentication $session)
    {
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->authenticate($this->credentials($request));

        return $handler->handle($request);
    }

    abstract protected function credentials(ServerRequestInterface $request): array;
}
