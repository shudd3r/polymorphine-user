<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Services;

use Polymorphine\Csrf\CsrfContext;
use Polymorphine\Headers\ResponseHeaders;
use Polymorphine\Session\SessionContext;


class ProcessContextServices
{
    protected $sessionCookie;

    private $responseHeaders;
    private $sessionContext;
    private $csrfContext;

    public function __construct(string $sessionName, array $cookieDirectives = [])
    {
        $this->sessionCookie = $this->responseHeaders()
                                    ->cookieSetup()
                                    ->directives($cookieDirectives)
                                    ->sessionCookie($sessionName);
    }

    public function responseHeaders()
    {
        return $this->responseHeaders
            ?: $this->responseHeaders = new ResponseHeaders();
    }

    public function sessionContext()
    {
        return $this->sessionContext
            ?: $this->sessionContext = new SessionContext\NativeSessionContext($this->sessionCookie);
    }

    public function csrfContext()
    {
        return $this->csrfContext
            ?: $this->csrfContext = new CsrfContext\PersistentTokenContext($this->sessionContext()->data());
    }
}
