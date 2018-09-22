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

use Polymorphine\Http\Context\CsrfProtection;
use Polymorphine\Http\Context\CsrfProtection\CsrfToken;


class MockedCsrfProtection implements CsrfProtection
{
    public $tokenReset = false;

    public function appSignature(): CsrfToken
    {
    }

    public function resetToken(): void
    {
        $this->tokenReset = true;
    }
}
