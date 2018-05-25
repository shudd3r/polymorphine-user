<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User;

use Polymorphine\User\Data\Credentials;
use Polymorphine\User\Data\DbRecord;


abstract class Repository
{
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function getUser(Credentials $credentials): AuthenticatedUser
    {
        $record = $this->record($credentials);
        return $record && ($credentials->id || $credentials->match($record))
            ? $this->factory->create($record)
            : $this->getAnonymousUser();
    }

    public function getAnonymousUser(): AuthenticatedUser
    {
        return $this->factory->anonymous();
    }

    abstract protected function record(Data $data): ?DbRecord;
}
