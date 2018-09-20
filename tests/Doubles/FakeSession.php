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

use Polymorphine\Http\Context\Session\SessionData;
use Polymorphine\Http\Context\Session;


class FakeSession implements Session
{
    public $data = [];
    public $regeneratedId = false;

    /** @var SessionData */
    private $storage;

    public function start(): void
    {
    }

    public function data(): SessionData
    {
        return $this->storage ?: $this->storage = new SessionData($this, $this->data);
    }

    public function resetContext(): void
    {
        $this->regeneratedId = true;
    }

    public function commit(array $data): void
    {
        $this->data = $data;
    }

    public function getData()
    {
        if ($this->storage) { $this->storage->commit(); }
        $this->storage = null;
        return $this->data;
    }
}