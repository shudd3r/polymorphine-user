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
use Polymorphine\User\Authentication\TokenAuthentication;
use Polymorphine\User\Tests\Doubles\MockedCookie;
use Polymorphine\User\Authentication\Token\PersistentCookieToken;
use Polymorphine\User\UserSession;
use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\MockedUsersRepository;
use Polymorphine\User\Tests\Doubles\MockedSession;


class CookieAuthenticationTest extends TestCase
{
    /** @var MockedCookie */
    private $cookie;

    /** @var MockedSession */
    private $session;

    /** @var MockedUsersRepository */
    private $users;

    public function testInstantiation()
    {
        $this->assertInstanceOf(TokenAuthentication::class, $this->auth());
    }

    public function testSuccessfulAuthentication()
    {
        $token   = ['tokenKey' => 'key', 'token' => 'hash'];
        $cookie  = $token['tokenKey'] . PersistentCookieToken::TOKEN_SEPARATOR . $token['token'];
        $request = $this->request($cookie);

        $this->assertTrue($this->auth(true)->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($token), $this->users->credentialsUsed);
        $this->assertSame(1, $this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertFalse($this->cookie->deleted);
        $this->assertTrue($this->session->regeneratedId);
    }

    public function testMissingCookie()
    {
        $auth    = $this->auth(true);
        $request = $this->request(false);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertFalse($this->cookie->deleted);
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testNotMatchingCookieToken()
    {
        $token   = ['tokenKey' => 'key', 'token' => 'hash'];
        $cookie  = $token['tokenKey'] . PersistentCookieToken::TOKEN_SEPARATOR . $token['token'];
        $request = $this->request($cookie);

        $this->assertFalse($this->auth(false)->authenticate($request)->isLoggedIn());
        $this->assertEquals(new Credentials($token), $this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertTrue($this->cookie->deleted);
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testInvalidToken()
    {
        $auth    = $this->auth(true);
        $request = $this->request('invalidString');

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->users->credentialsUsed);
        $this->assertNull($this->session->data()->get(UserSession::SESSION_USER_KEY));
        $this->assertTrue($this->cookie->deleted);
        $this->assertFalse($this->session->regeneratedId);
    }

    private function request(?string $cookie = null)
    {
        $request = new FakeServerRequest();
        if ($cookie) {
            $request->cookies[MockedCookie::COOKIE_NAME] = $cookie;
        }
        return $request;
    }

    private function auth($success = true)
    {
        $this->cookie  = new MockedCookie();
        $this->session = new MockedSession();

        $this->users = $success
            ? new MockedUsersRepository(new FakeAuthUser(1, 'Username'))
            : new MockedUsersRepository();

        return new TokenAuthentication(
            new UserSession($this->session->data(), $this->users),
            new PersistentCookieToken($this->cookie, $this->users)
        );
    }
}
