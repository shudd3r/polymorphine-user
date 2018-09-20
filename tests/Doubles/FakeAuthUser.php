<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Doubles;

use Polymorphine\User\AuthenticatedUser;


class FakeAuthUser implements AuthenticatedUser
{
    private $id;
    private $name;

    public function __construct(int $id, string $name = 'none')
    {
        $this->id   = $id;
        $this->name = $name;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function isLoggedIn(): bool
    {
        return !empty($this->id);
    }

    public function name()
    {
        return $this->name;
    }
}
