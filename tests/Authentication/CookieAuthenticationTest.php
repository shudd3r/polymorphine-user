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
    private $session;

    public function testInstantiation()
    {
        $this->assertInstanceOf(Authentication::class, $this->auth());
    }

    public function testSuccessfulAuth()
    {
        $auth = $this->auth(true);

        $request = new FakeServerRequest('GET');
        $request->cookies[PersistentAuthCookie::COOKIE_NAME] = 'key' . PersistentAuthCookie::TOKEN_SEPARATOR . 'hash';

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertSame(['userId' => 1], $this->session->getData());
        $this->assertTrue($this->session->regeneratedId);
    }

    public function testFailedAuth()
    {
        $auth = $this->auth(false);

        $request = new FakeServerRequest('GET');
        $request->cookies[PersistentAuthCookie::COOKIE_NAME] = 'key' . PersistentAuthCookie::TOKEN_SEPARATOR . 'hash';

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertSame([], $this->session->getData());
        $this->assertFalse($this->session->regeneratedId);
        $cookie = $this->headers->data['Set-Cookie'][0];
        $this->assertSame('remember=', substr($cookie, 0, strpos($cookie, ';')));
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
