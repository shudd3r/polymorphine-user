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

use Polymorphine\User\Data\Credentials;
use Psr\Http\Message\ServerRequestInterface;


interface Token
{
    public function enableForUser($userId): void;

    public function credentials(ServerRequestInterface $request): ?Credentials;

    public function revoke(): void;
}
