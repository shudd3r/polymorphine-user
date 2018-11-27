<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\ServiceProviders;

use Polymorphine\Csrf\CsrfContext;
use Polymorphine\Headers\ResponseHeaders;
use Polymorphine\Session\SessionContext\NativeSessionContext;
use Polymorphine\Session\SessionContext\SessionData;


class SessionContextServices
{
    protected $sessionName;
    protected $cookieDirectives;

    private $responseHeaders;
    private $sessionData;
    private $csrfContext;

    public function __construct(string $name = 'PHPSESSID', array $directives = [])
    {
        $this->sessionName      = $name;
        $this->cookieDirectives = $directives;
    }

    public function responseHeaders(): ResponseHeaders
    {
        return $this->responseHeaders ?: $this->responseHeaders = new ResponseHeaders();
    }

    public function sessionData(): SessionData
    {
        return $this->sessionData ?: $this->sessionData = $this->createSessionData();
    }

    public function csrfContext()
    {
        return $this->csrfContext ?: $this->csrfContext = $this->createCsrfContext();
    }

    protected function createSessionData(): SessionData
    {
        $cookieSetup = $this->responseHeaders()->cookieSetup()->directives($this->cookieDirectives);
        $context     = new NativeSessionContext($cookieSetup->sessionCookie($this->sessionName));

        return $context->data();
    }

    protected function createCsrfContext(): CsrfContext
    {
        return new CsrfContext\PersistentTokenContext($this->sessionData());
    }
}
