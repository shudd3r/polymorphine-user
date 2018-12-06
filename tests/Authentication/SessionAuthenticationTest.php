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
use Polymorphine\User\Authentication\SessionAuthentication;
use Polymorphine\User\UserSession;
use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\MockedUsersRepository;
use Polymorphine\User\Tests\Doubles\MockedSession;
use Polymorphine\Session\SessionContext\SessionData;


class SessionAuthenticationTest extends TestCase
{
    /** @var MockedSession */
    private $session;

    /** @var MockedUsersRepository */
    private $users;

    public function testInstantiation()
    {
        $this->assertInstanceOf(SessionAuthentication::class, $this->auth());
    }

    public function testSessionDataWithoutUserId()
    {
        $this->assertFalse($this->auth(true)->authenticate(new FakeServerRequest())->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
    }

    public function testSessionResumed()
    {
        $userId = 1234;
        $auth   = $this->auth(true, [SessionData::USER_KEY => $userId]);

        $this->assertTrue($auth->authenticate(new FakeServerRequest())->isLoggedIn());
        $this->assertEquals(new Credentials(['id' => $userId]), $this->users->credentialsUsed);
    }

    public function testSessionWithInvalidUserId()
    {
        $userId = 1234;
        $auth   = $this->auth(false, [SessionData::USER_KEY => $userId]);

        $this->assertFalse($auth->authenticate(new FakeServerRequest())->isLoggedIn());
        $this->assertEquals(new Credentials(['id' => $userId]), $this->users->credentialsUsed);
    }

    private function auth(bool $success = true, array $session = [])
    {
        $this->session = new MockedSession($session);

        $this->users = $success
            ? new MockedUsersRepository(new FakeAuthUser(1, 'Username'))
            : new MockedUsersRepository();

        return new SessionAuthentication(
            new UserSession($this->session->data(), $this->users)
        );
    }
}
