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

use Polymorphine\Session\SessionContext;
use Polymorphine\Session\SessionStorage;
use Polymorphine\Session\SessionStorageProvider;


class MockedSession implements SessionContext, SessionStorageProvider
{
    public $regeneratedId = false;

    /** @var SessionStorage */
    private $storage;

    public function __construct(array $sessionData = [])
    {
        $this->storage = new SessionStorage($this, $sessionData);
    }

    public function start(): void
    {
    }

    public function storage(): SessionStorage
    {
        return $this->storage;
    }

    public function reset(): void
    {
        $this->regeneratedId = true;
    }

    public function commit(array $data): void
    {
    }
}
