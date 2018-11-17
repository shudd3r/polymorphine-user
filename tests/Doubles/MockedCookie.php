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

use Polymorphine\Headers\Cookie;


class MockedCookie implements Cookie
{
    public const COOKIE_NAME = 'RememberMe';

    public $value;
    public $deleted = false;

    public function name(): string
    {
        return self::COOKIE_NAME;
    }

    public function send(string $value): void
    {
        $this->value = $value;
    }

    public function revoke(): void
    {
        $this->deleted = true;
    }
}
