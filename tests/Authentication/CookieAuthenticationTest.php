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
use Polymorphine\User\Authentication\CookieAuthentication;
use Polymorphine\User\PersistentAuthCookie;
use Polymorphine\User\UserSession;
use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\MockedUsersRepository;
use Polymorphine\User\Tests\Doubles\MockedResponseHeaders;
use Polymorphine\User\Tests\Doubles\MockedSession;


class CookieAuthenticationTest extends TestCase
{
    /** @var MockedResponseHeaders */
    private $headers;

    /** @var MockedSession */
    private $session;

    /** @var MockedUsersRepository */
    private $users;

    public function testInstantiation()
    {
        $this->assertInstanceOf(CookieAuthentication::class, $this->auth());
    }

    public function testSuccessfulAuthentication()
    {
        $token   = ['tokenKey' => 'key', 'token' => 'hash'];
        $cookie  = $token['tokenKey'] . PersistentAuthCookie::TOKEN_SEPARATOR . $token['token'];
        $request = $this->request($cookie);

        $this->assertTrue($this->auth(true)->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($token), $this->users->credentialsUsed);
        $this->assertSame(1, $this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertFalse(isset($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]));
        $this->assertTrue($this->session->regeneratedId);
    }

    public function testMissingCookie()
    {
        $auth    = $this->auth(true);
        $request = $this->request(false);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertFalse(isset($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]));
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testNotMatchingCookieToken()
    {
        $token   = ['tokenKey' => 'key', 'token' => 'hash'];
        $cookie  = $token['tokenKey'] . PersistentAuthCookie::TOKEN_SEPARATOR . $token['token'];
        $request = $this->request($cookie);

        $this->assertFalse($this->auth(false)->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($token), $this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertTrue($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]);
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testInvalidToken()
    {
        $auth    = $this->auth(true);
        $request = $this->request('invalidString');

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertTrue($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]);
        $this->assertFalse($this->session->regeneratedId);
    }

    private function request(?string $cookie = null)
    {
        $request = new FakeServerRequest();
        if ($cookie) {
            $request->cookies[PersistentAuthCookie::COOKIE_NAME] = $cookie;
        }
        return $request;
    }

    private function auth($success = true)
    {
        $this->headers = new MockedResponseHeaders();
        $this->session = new MockedSession();

        $this->users = $success
            ? new MockedUsersRepository(new FakeAuthUser(1, 'Username'))
            : new MockedUsersRepository();

        return new CookieAuthentication(
            new UserSession($this->session, $this->users),
            new PersistentAuthCookie($this->headers, $this->users)
        );
    }
}
