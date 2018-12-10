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

use Polymorphine\User\Authentication\PasswordAuthentication;
use PHPUnit\Framework\TestCase;
use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\MockedSession;
use Polymorphine\User\Tests\Doubles\MockedUsersRepository;
use Polymorphine\User\UserSession;


class PasswordAuthenticationTest extends TestCase
{
    /** @var MockedSession */
    private $session;

    /** @var MockedUsersRepository */
    private $users;

    public function testInstantiation()
    {
        $this->assertInstanceOf(PasswordAuthentication::class, $this->auth());
    }

    public function testGetMethodSkipAuthentication()
    {
        $request = new FakeServerRequest('GET');
        $auth    = $this->auth(true);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->storage()->userId());
    }

    public function testSuccessfulLoginAuthentication()
    {
        $credentials = ['name' => 'user', 'password' => 'pass'];
        $request     = $this->request($credentials['name'], $credentials['password']);
        $auth        = $this->auth(true);

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($credentials), $this->users->credentialsUsed);
        $this->assertSame(1, $this->session->storage()->userId());
    }

    public function testSuccessfulEmailAuthentication()
    {
        $credentials = ['email' => 'user@example.com', 'password' => 'pass'];
        $request     = $this->request($credentials['email'], $credentials['password']);
        $auth        = $this->auth(true);

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($credentials), $this->users->credentialsUsed);
        $this->assertSame(1, $this->session->storage()->userId());
    }

    public function testMissingAuthData()
    {
        $credentials = ['name' => 'user', 'password' => 'pass'];
        $request     = $this->request($credentials['name']);
        $auth        = $this->auth(true);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->storage()->userId());
    }

    private function request(string $username = null, string $password = null)
    {
        $request = new FakeServerRequest('POST');
        if ($username || $password) {
            $request->parsed = [
                PasswordAuthentication::USER_LOGIN_FIELD => $username,
                PasswordAuthentication::USER_PASS_FIELD  => $password
            ];
        }
        return $request;
    }

    private function auth(bool $success = true)
    {
        $this->session = new MockedSession();

        $this->users = $success
            ? new MockedUsersRepository(new FakeAuthUser(1, 'Username'))
            : new MockedUsersRepository();

        return new PasswordAuthentication(
            new UserSession($this->session->storage(), $this->users)
        );
    }
}
