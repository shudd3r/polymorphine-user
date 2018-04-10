<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Session;

use Polymorphine\User\Tests\Fixtures\SessionGlobalState;

function session_start()
{
    global $_SESSION;

    $_SESSION = SessionGlobalState::$sessionData;
}

function session_status()
{
    return SessionGlobalState::$sessionStatus;
}
