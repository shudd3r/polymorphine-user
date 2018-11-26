<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\Tests;

use PHPUnit\Framework\TestCase;
use Polymorphine\User\AuthMiddleware;
use Polymorphine\User\Tests\Doubles\FakeAuthentication;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\MockedRequestHandler;


class AuthMiddlewareTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(AuthMiddleware::class, $this->auth(true));
    }

    public function testSingleMethodSuccessAuth()
    {
        $this->processAuth($handler, true);
        $this->assertTrue($handler->authenticated);
    }

    public function testSingleMethodFailedAuth()
    {
        $this->processAuth($handler, false);
        $this->assertSame('undefined', $handler->authenticated);
    }

    public function testMultipleMethodsSuccessfulAuth()
    {
        $this->processAuth($handler, false, false, true, false);
        $this->assertTrue($handler->authenticated);
    }

    public function testMultipleMethodsFailedAuth()
    {
        $this->processAuth($handler, false, false, false);
        $this->assertSame('undefined', $handler->authenticated);
    }

    private function processAuth(&$handler, bool ...$results): void
    {
        $this->auth(...$results)->process(new FakeServerRequest(), $handler = new MockedRequestHandler());
    }

    private function auth(bool ...$results): AuthMiddleware
    {
        $methods = [];
        foreach ($results as $result) {
            $methods[] = new FakeAuthentication($result);
        }
        return new AuthMiddleware(...$methods);
    }
}
