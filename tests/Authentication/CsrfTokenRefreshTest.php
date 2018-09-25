<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Authentication;

use PHPUnit\Framework\TestCase;
use Polymorphine\User\Authentication\CsrfTokenRefresh;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeAuthentication;
use Polymorphine\User\Tests\Doubles\MockedCsrfProtection;


class CsrfTokenRefreshTest extends TestCase
{
    /** @var MockedCsrfProtection */
    private $csrfProtection;

    public function testInstantiation()
    {
        $this->assertInstanceOf(CsrfTokenRefresh::class, $this->auth());
    }

    public function testSuccessfulAuth()
    {
        $auth = $this->auth(true);
        $this->assertTrue($auth->authenticate(new FakeServerRequest())->isLoggedIn());
        $this->assertTrue($this->csrfProtection->tokenReset);
    }

    public function testFailedAuth()
    {
        $auth = $this->auth(false);
        $this->assertFalse($auth->authenticate(new FakeServerRequest())->isLoggedIn());
        $this->assertFalse($this->csrfProtection->tokenReset);
    }

    private function auth(bool $success = true)
    {
        $this->csrfProtection = new MockedCsrfProtection();
        return new CsrfTokenRefresh(new FakeAuthentication($success), $this->csrfProtection);
    }
}
