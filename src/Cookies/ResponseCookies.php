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


class ResponseCookies
{
    private $headers;

    public function cookie(string $name): CookieSetup
    {
        return new CookieSetup($name, $this);
    }

    public function addHeader(string $cookieHeader): void
    {
        $this->headers[] = $cookieHeader;
    }

    public function setHeaders(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->headers as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', $cookie);
        }

        return $response;
    }
}
