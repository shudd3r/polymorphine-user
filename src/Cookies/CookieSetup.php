<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Cookies;


class CookieSetup
{
    private $cookieJar;

    public function build(array $cookies)
    {
        $this->cookieJar = new CookieJar($cookies);
    }

    public function collection(): CookieJar
    {
        return $this->cookieJar ?? $this->cookieJar = new CookieJar([]);
    }
}