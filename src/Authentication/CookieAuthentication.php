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
use Polymorphine\Http\Server\Response\ResponseHeaders;
use Polymorphine\Http\Server\Session\SessionStorage;


class CookieAuthentication implements Authentication
{
    protected const REMEMBER_COOKIE = 'remember';

    private $headers;
    private $session;
    private $identities;

    public function __construct(ResponseHeaders $headers, SessionStorage $session, Identification $identities)
    {
        $this->headers    = $headers;
        $this->session    = $session;
        $this->identities = $identities;
    }

    public function authenticate(array $credentials): ?int
    {
        $token = $credentials[self::REMEMBER_COOKIE] ?? null;

        if (!$token) { return null; }

        ($id = $this->identities->getIdByCookieToken($token))
            ? $this->session->set(SessionAuthentication::USER_ID_KEY, $id)
            : $this->headers->cookie(self::REMEMBER_COOKIE)->remove();

        return $id ?? null;
    }
}
