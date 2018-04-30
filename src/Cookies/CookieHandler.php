<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class CookieHandler implements MiddlewareInterface
{
    private $cookies;

    public function __construct(CookieJar $cookies)
    {
        $this->cookies = $cookies;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        foreach ($this->cookies as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', (string) $cookie);
        }

        return $response;
    }
}
