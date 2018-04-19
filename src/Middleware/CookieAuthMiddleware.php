<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Middleware;

use Polymorphine\User\AuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class CookieAuthMiddleware extends AuthMiddleware
{
    protected function credentials(ServerRequestInterface $request): array
    {
        $cookies = $request->getCookieParams();

        return [
            'session'  => isset($cookies[session_name()]),
            'remember' => $cookies['remember'] ?? null
        ];
    }

    protected function setTokens(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->authentication->tokens() as $name => $value) {
            $response = $response->withAddedHeader('Set-Cookie', $value);
        }

        return $response;
    }
}
