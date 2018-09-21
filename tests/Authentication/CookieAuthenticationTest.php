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
use Polymorphine\User\Authentication\CookieAuthentication;
use Polymorphine\User\PersistentAuthCookie;
use Polymorphine\User\Tests\Doubles\FakeAuthUser;
use Polymorphine\User\Tests\Doubles\FakeResponseHeaders;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeSession;
use Polymorphine\User\Tests\Doubles\FakeUsersRepository;
use Polymorphine\User\UserSession;


class CookieAuthenticationTest extends TestCase
{
    private $headers;

    /** @var FakeSession */
    private $session;

    public function testInstantiation()
    {
        $this->assertInstanceOf(Authentication::class, $this->auth());
    }

    public function testSuccessfulAuthentication()
    {
        $auth    = $this->auth(true);
        $request = $this->request(true);

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertSame(1, $this->session->data()->get('userId'));
        $this->assertTrue($this->session->regeneratedId);
    }

    public function testMissingCookie()
    {
        $auth    = $this->auth(true);
        $request = $this->request(false);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->session->data()->get('userId'));
        $this->assertFalse(isset($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]));
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testNotMatchingCookieToken()
    {
        $auth    = $this->auth(false);
        $request = $this->request(true);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->session->data()->get('userId'));
        $this->assertTrue($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]);
        $this->assertFalse($this->session->regeneratedId);
    }

    public function testInvalidToken()
    {
        $auth    = $this->auth(true);
        $request = $this->request(false);
        $request->cookies[PersistentAuthCookie::COOKIE_NAME] = 'InvalidString';

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertNull($this->session->data()->get('userId'));
        $this->assertTrue($this->headers->cookiesRemoved[PersistentAuthCookie::COOKIE_NAME]);
        $this->assertFalse($this->session->regeneratedId);
    }

    private function request($cookie = true)
    {
        $request = new FakeServerRequest('GET');
        if ($cookie) {
            $token = 'key' . PersistentAuthCookie::TOKEN_SEPARATOR . 'hash';
            $request->cookies[PersistentAuthCookie::COOKIE_NAME] = $token;
        }
        return $request;
    }

    private function auth($success = true)
    {
        $this->headers = new FakeResponseHeaders();
        $this->session = new FakeSession();

        $userRepo = $success
            ? new FakeUsersRepository(new FakeAuthUser(1, 'Username'))
            : new FakeUsersRepository();

        return new CookieAuthentication(
            new UserSession($this->session, $userRepo),
            new PersistentAuthCookie($this->headers, $userRepo)
        );
    }
}
