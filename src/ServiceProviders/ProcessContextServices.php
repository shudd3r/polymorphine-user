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
use Polymorphine\Session\SessionContext;
use Polymorphine\Session\SessionContext\NativeSessionContext;


class ProcessContextServices
{
    protected $sessionName;
    protected $cookieDirectives;

    private $responseHeaders;
    private $sessionContext;
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

    public function sessionContext(): SessionContext
    {
        return $this->sessionContext ?: $this->sessionContext = $this->createSessionContext();
    }

    public function csrfContext(): CsrfContext
    {
        return $this->csrfContext ?: $this->csrfContext = $this->createCsrfContext();
    }

    protected function createSessionContext(): SessionContext
    {
        $cookieSetup = $this->responseHeaders()->cookieSetup()->directives($this->cookieDirectives);
        return new NativeSessionContext($cookieSetup->sessionCookie($this->sessionName));
    }

    protected function createCsrfContext(): CsrfContext
    {
        return new CsrfContext\PersistentTokenContext($this->sessionContext()->data());
    }
}
