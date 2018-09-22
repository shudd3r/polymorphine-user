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

use Polymorphine\Http\Context\ResponseHeaders;
use Polymorphine\Http\Context\ResponseHeaders\CookieSetup;


class MockedResponseHeaders implements ResponseHeaders
{
    public $data           = [];
    public $cookiesRemoved = [];

    public function cookie(string $name, array $attributes = []): CookieSetup
    {
        return new FakeCookieSetup($name, $this, $attributes);
    }

    public function add(string $name, string $header): void
    {
        $this->data[$name][] = $header;
    }
}
