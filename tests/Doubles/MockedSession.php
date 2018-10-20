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

use Polymorphine\App\Context\Session\SessionData;
use Polymorphine\App\Context\Session;


class MockedSession implements Session
{
    public $regeneratedId = false;

    /** @var SessionData */
    private $storage;

    public function __construct(array $sessionData = [])
    {
        $this->storage = new SessionData($this, $sessionData);
    }

    public function start(): void
    {
    }

    public function data(): SessionData
    {
        return $this->storage;
    }

    public function resetContext(): void
    {
        $this->regeneratedId = true;
    }

    public function commit(array $data): void
    {
    }
}
