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

use Polymorphine\User\Authentication\SessionAuthentication;
use PHPUnit\Framework\TestCase;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeUsersRepository;
use Polymorphine\User\Tests\Doubles\MockedSession;
use Polymorphine\User\UserSession;


class SessionAuthenticationTest extends TestCase
{
    /** @var MockedSession */
    private $session;

    /** @var FakeUsersRepository */
    private $users;

    public function testInstantiation()
    {
        $this->assertInstanceOf(SessionAuthentication::class, $this->auth());
    }

    public function testSessionDataWithoutUserId()
    {
        $this->assertFalse($this->auth(true)->authenticate(new FakeServerRequest())->isLoggedIn());
    }

    public function testSessionSuccessfullyResumed()
    {
        $auth = $this->auth(true);
        $this->session->data()->set(UserSession::SESSION_USER_KEY, 1);

        $this->assertTrue($auth->authenticate(new FakeServerRequest())->isLoggedIn());
    }

    public function testSessionWithInvalidUserId()
    {
        $auth = $this->auth(false);
        $this->session->data()->set(UserSession::SESSION_USER_KEY, 1);

        $this->assertFalse($auth->authenticate(new FakeServerRequest())->isLoggedIn());
    }

    private function auth($success = true)
    {
        $this->session = new MockedSession();

        $this->users = $success
            ? new FakeUsersRepository(new FakeAuthUser(1, 'Username'))
            : new FakeUsersRepository();

        return new SessionAuthentication(
            new UserSession($this->session, $this->users)
        );
    }
}
