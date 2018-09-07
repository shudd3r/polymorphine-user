<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User;

use Polymorphine\Http\Context\SessionManager;
use Polymorphine\User\Data\Credentials;


class UserSession
{
    public const SESSION_USER_KEY = 'userId';
    public const USER_LOGIN_FIELD = 'username';
    public const USER_PASS_FIELD  = 'password';
    public const REMEMBER_COOKIE  = 'remember';
    public const TOKEN_SEPARATOR  = ':';

    private $authentication;
    private $sessionManager;

    public function __construct(Authentication $authentication, SessionManager $sessionManager)
    {
        $this->authentication = $authentication;
        $this->sessionManager = $sessionManager;
    }

    public function resume()
    {
        $id = $this->sessionManager->session()->get(static::SESSION_USER_KEY);
        if (!$id) { return null; }

        $user = $this->authentication->signIn(new Credentials(['id' => $id]));
        if (!$user->isLoggedIn()) {
            $this->sessionManager->session()->clear();
            return null;
        }

        return $id;
    }

    public function signIn(Credentials $credentials)
    {
        $user = $this->authentication->signIn($credentials);
        if (!$user->isLoggedIn()) { return null; }

        $id = $user->id();
        $this->sessionManager->session()->set(static::SESSION_USER_KEY, $id);
        $this->sessionManager->regenerateId();

        return $id;
    }
}
