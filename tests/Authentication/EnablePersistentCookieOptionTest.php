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
use Polymorphine\User\Authentication\EnablePersistentCookieOption;
use Polymorphine\User\PersistentAuthCookie as AuthCookie;
use Polymorphine\User\Tests\Doubles\FakeAuthentication;
use Polymorphine\User\Tests\Doubles\FakeServerRequest;
use Polymorphine\User\Tests\Doubles\FakeUsersRepository;
use Polymorphine\User\Tests\Doubles\MockedResponseHeaders;


class EnablePersistentCookieOptionTest extends TestCase
{
    /** @var MockedResponseHeaders */
    private $headers;

    /** @var FakeUsersRepository */
    private $repository;

    public function testInstantiation()
    {
        $this->assertInstanceOf(EnablePersistentCookieOption::class, $this->auth());
    }

    public function testFailedAuthentication()
    {
        $auth    = $this->auth(false);
        $request = $this->request(true);

        $this->assertFalse($auth->authenticate($request)->isLoggedIn());
        $this->assertFalse(isset($this->repository->token));
        $this->assertFalse(isset($this->headers->data['Set-Cookie']));
    }

    public function testSuccessfulAuthentication()
    {
        $auth    = $this->auth(true);
        $request = $this->request(true);

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertTrue(isset($this->repository->token));
        $this->assertTrue(isset($this->headers->data['Set-Cookie']));

        $cookie = $this->headers->cookieValue[AuthCookie::COOKIE_NAME];
        [$key, $token] = explode(AuthCookie::TOKEN_SEPARATOR, $cookie);
        $this->assertSame(['id' => 1, 'key' => $key, 'token' => hash('sha256', $token)], $this->repository->token);
    }

    public function testOptionUnchecked()
    {
        $auth    = $this->auth(true);
        $request = $this->request(false);

        $this->assertTrue($auth->authenticate($request)->isLoggedIn());
        $this->assertFalse(isset($this->repository->token));
        $this->assertFalse(isset($this->headers->data['Set-Cookie']));
    }

    private function request($optionChecked = true)
    {
        $request = new FakeServerRequest();
        if ($optionChecked) {
            $request->parsed[EnablePersistentCookieOption::REMEMBER_FIELD] = true;
        }
        return $request;
    }

    private function auth(bool $success = true)
    {
        $this->headers    = new MockedResponseHeaders();
        $this->repository = new FakeUsersRepository();

        return new EnablePersistentCookieOption(
            new FakeAuthentication($success),
            new AuthCookie($this->headers, $this->repository)
        );
    }
}
