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
use Polymorphine\User\Authentication;
use Polymorphine\User\Authentication\SessionAuthentication;
use Polymorphine\User\Tests\Fixtures\Doubles\FakeRepository;
use Polymorphine\User\Tests\Fixtures\Doubles\FakeSession;


class SessionAuthenticationTest extends TestCase
{
    private function auth(array $session = [], string $id = '')
    {
        return new SessionAuthentication(new FakeSession($session), new FakeRepository($id));
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Authentication::class, $this->auth());
    }

    public function testUserIsAuthenticatedWithSessionId()
    {
        $auth = $this->auth(['id' => '334'], '334');
        $auth->credentials(['session' => true]);

        $this->assertSame('334', $auth->user()->id());
    }

    public function testUserIsAuthenticatedWithRememberToken()
    {
        $auth = $this->auth(['id' => '334'], 'FF34E4A0');
        $auth->credentials(['remember' => 'FF34E4A0']);

        $this->assertSame('FF34E4A0', $auth->user()->id());
    }

    public function testNotMatchingIdAuthenticatedAsGuest()
    {
        $auth = $this->auth(['id' => '334'], '335');
        $auth->credentials(['session' => true]);

        $this->assertSame('', $auth->user()->id());

        $auth = $this->auth(['id' => '334'], '335');
        $auth->credentials(['session' => true, 'remember' => 'ABCD']);

        $this->assertSame('', $auth->user()->id());

        $auth = $this->auth(['id' => '334'], 'ABC');
        $auth->credentials(['remember' => 'XYZ']);

        $this->assertSame('', $auth->user()->id());
    }
}
