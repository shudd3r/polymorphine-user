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
use Polymorphine\User\Session;
use Polymorphine\User\User;
use Polymorphine\User\Repository;
use Psr\Http\Message\ServerRequestInterface;


class SessionAuthentication implements Authentication
{
    protected const SESSION_ID = 'id';

    private $session;
    private $users;

    public function __construct(Session $session, Repository $users)
    {
        $this->session = $session;
        $this->users   = $users;
    }

    public function credential(ServerRequestInterface $request): void
    {
    }

    public function user(): User
    {
        $id = $this->session->get(self::SESSION_ID);

        return $this->users->getUserById($id);
    }
}
