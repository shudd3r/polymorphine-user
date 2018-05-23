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

use Polymorphine\User\Repository;
use Polymorphine\User\UserData;


class Identification
{
    protected const TOKEN_SEPARATOR = ':';

    public $repository;
    public $factory;

    public function __construct(Repository $repository, $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }

    public function confirmId(string $id): bool
    {
        $user = new UserData(['id' => $id]);
        $user = $this->repository->match($user);

        return isset($user);
    }

    public function getIdByCookieToken(string $token): ?string
    {
        [$key, $hash] = explode(self::TOKEN_SEPARATOR, $token);

        $user = new UserData(['tokenKey' => $key]);
        $user = $this->repository->match($user);

        if (!isset($user) || !$user->tokenHash) { return null; }

        //TODO: Hash check

        return $user->id ?? null;
    }
}
