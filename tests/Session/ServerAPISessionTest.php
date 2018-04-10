<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Tests\Session;

use PHPUnit\Framework\TestCase;
use Polymorphine\User\Session;
use Polymorphine\User\Session\ServerAPISession;
use Polymorphine\User\Tests\Fixtures\SessionGlobalState;
use RuntimeException;

require_once dirname(__DIR__) . '/Fixtures/session-functions.php';


class ServerAPISessionTest extends TestCase
{
    public function setUp()
    {
        SessionGlobalState::$sessionData = [];
        SessionGlobalState::$sessionStatus = PHP_SESSION_NONE;
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Session::class, $this->session());
    }

    public function testValuesNotAffectedByGlobalState()
    {
        $session = $this->session();

        $_SESSION['one'] = '1';
        $session->set('key', 'value');
        $_SESSION['two'] = '2';

        unset($session);

        $this->assertSame(['key' => 'value'], $_SESSION);
    }

    public function testSessionStartedElsewhere_ThrowsException()
    {
        SessionGlobalState::$sessionStatus = PHP_SESSION_ACTIVE;
        $this->expectException(RuntimeException::class);
        $this->session();
    }

    public function testSessionValuesCanBeRetrieved()
    {
        SessionGlobalState::$sessionData = ['key' => 'value'];
        $this->assertSame('value', $this->session()->get('key'));
    }

    public function testGetForUndefinedKey_ReturnsDefaultValue()
    {
        $this->assertSame('default value', $this->session()->get('undefinedKey', 'default value'));
    }

    public function testClearValue()
    {
        SessionGlobalState::$sessionData = ['key1' => 'value1', 'key2' => 'value2'];
        $session = $this->session();
        $this->assertTrue($session->exists('key1'));

        $session->clear('key1');
        $this->assertFalse($session->exists('key1'));

        unset($session);
        $this->assertSame(['key2' => 'value2'], $_SESSION);
    }

    private function session()
    {
        return new ServerAPISession();
    }
}
