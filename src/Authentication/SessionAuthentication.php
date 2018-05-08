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
use Polymorphine\User\Session\SessionStorage;


class SessionAuthentication implements Authentication
{
    private $session;
    private $identities;

    public function __construct(SessionStorage $session, Identification $identities)
    {
        $this->session    = $session;
        $this->identities = $identities;
    }

    public function authenticate(array $credentials): ?int
    {
        if (!$id = $this->session->get($this->session::USER_ID_KEY)) { return null; };

        if (!$this->identities->confirmId($id)) {
            $this->session->clear($this->session::USER_ID_KEY);
        }

        return $id ?? null;
    }
}
