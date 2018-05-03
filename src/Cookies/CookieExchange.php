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


class CookieExchange implements MiddlewareInterface
{
    private $cookies;

    public function __construct(CookieSetup $cookies)
    {
        $this->cookies = $cookies;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->readCookies($request);

        $response = $handler->handle($request);

        return $this->writeCookies($response);
    }

    protected function readCookies(ServerRequestInterface $request): void
    {
        $this->cookies->build($request->getCookieParams());
    }

    protected function writeCookies(ResponseInterface $response): ResponseInterface
    {
        $cookieJar = $this->cookies->collection();

        foreach ($cookieJar->responseCookies() as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', (string) $cookie);
        }

        return $response;
    }
}
