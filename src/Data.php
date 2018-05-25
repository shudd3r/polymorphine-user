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


class Data
{
    public $id;
    public $name;
    public $email;
    public $tokenKey;

    public $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;

        $this->id       = (int) $this->pullFromData('id');
        $this->name     = (string) $this->pullFromData('name');
        $this->email    = (string) $this->pullFromData('email');
        $this->tokenKey = (string) $this->pullFromData('tokenKey');
    }

    protected function pullFromData(string $key): array
    {
        $value = $this->data[$key] ?? null;
        unset($this->data[$key]);

        return $value;
    }
}
