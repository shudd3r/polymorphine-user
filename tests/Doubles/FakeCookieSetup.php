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

use Polymorphine\Http\Context\ResponseHeaders\CookieSetup;


class FakeCookieSetup extends CookieSetup
{
    private $name;
    private $headers;

    public function __construct(string $name, MockedResponseHeaders $headers, array $attributes = [])
    {
        $this->name    = $name;
        $this->headers = $headers;
        parent::__construct($name, $headers, $attributes);
    }

    public function value(string $value): void
    {
        $this->headers->cookieValue[$this->name] = $value;
        parent::value($value);
    }

    public function remove(): void
    {
        $this->headers->cookiesRemoved[$this->name] = true;
        parent::remove();
    }
}
