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
use Polymorphine\User\UserSession;
use Polymorphine\User\Data\Credentials;
use Psr\Http\Message\ServerRequestInterface;


class PasswordAuthentication implements Authentication
{
    public const USER_LOGIN_FIELD = 'username';
    public const USER_PASS_FIELD  = 'password';

    private $userSession;

    public function __construct(UserSession $userSession)
    {
        $this->userSession = $userSession;
    }

    public function authenticate(ServerRequestInterface $request): AuthenticatedUser
    {
        if ($request->getMethod() !== 'POST') {
            return $this->userSession->user();
        }

        $payload     = $request->getParsedBody();
        $credentials = $this->credentials($payload);

        return $credentials ? $this->userSession->signIn($credentials) : $this->userSession->user();
    }

    private function credentials(array $data): ?Credentials
    {
        $login    = $data[static::USER_LOGIN_FIELD] ?? null;
        $password = $data[static::USER_PASS_FIELD] ?? null;

        if (!$login || !$password) { return null; }

        $email = filter_var($login, FILTER_VALIDATE_EMAIL) ?: null;
        return new Credentials([
            'name'     => $email ? null : $login,
            'email'    => $email,
            'password' => $password
        ]);
    }
}
