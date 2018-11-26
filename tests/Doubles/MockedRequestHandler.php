<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Doubles;

use Polymorphine\User\Authentication;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class MockedRequestHandler implements RequestHandlerInterface
{
    public $authenticated = false;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->authenticated = $request->getAttribute(Authentication::AUTH_ATTR, 'undefined');
        return new DummyResponse();
    }
}
