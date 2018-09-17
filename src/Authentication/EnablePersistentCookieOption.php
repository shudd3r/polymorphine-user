<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Authentication;

use Polymorphine\User\Authentication;
use Polymorphine\User\AuthenticatedUser;
use Polymorphine\User\PersistentAuthCookie;
use Psr\Http\Message\ServerRequestInterface;


class EnablePersistentCookieOption implements Authentication
{
    public const REMEMBER_FIELD = 'remember';

    private $auth;
    private $cookie;

    public function __construct(Authentication $auth, PersistentAuthCookie $cookie)
    {
        $this->auth   = $auth;
        $this->cookie = $cookie;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        $user = $this->auth->authenticate($request);
        if (!$user->isLoggedIn()) { return $user; }

        $payload = $request->getParsedBody();
        if (isset($payload[static::REMEMBER_FIELD])) {
            $this->cookie->setToken($user->id());
        }

        return $user;
    }
}
