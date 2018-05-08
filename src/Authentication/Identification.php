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


class Identification
{
    public $database;
    public $factory;

    public function __construct($database, $factory)
    {
        $this->database = $database;
        $this->factory  = $factory;
    }

    public function confirmId(string $id): bool
    {
        $data = $this->database->findWhere(['id' => $id]);
        return !empty($data);
    }

    public function getIdByCookieToken(string $token): ?string
    {
        [$key, $hash] = explode(':', $token);

        $data = $this->database->findWhere(['tokenKey' => $key]);

        if (!$data || $data['tokenHash']) {
            //TODO: Hash check
        }

        return $data['id'] ?? null;
    }
}
