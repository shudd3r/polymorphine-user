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

use Psr\Http\Server\MiddlewareInterface;
use Polymorphine\User\Cookies\ResponseCookies;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;


class StartSessionContext implements MiddlewareInterface
{
    private $cookies;
    private $session;

    private $sessionContext = false;

    public function __construct(ResponseCookies $cookies, SessionDataStorage $session = null)
    {
        $this->session = $session;
        $this->cookies = $cookies;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session) { $this->startSession($request->getCookieParams()); }

        $response = $handler->handle($request);

        if ($this->session) { $this->closeSession($this->session->getAll()); }

        return $this->cookies->setHeaders($response);
    }

    private function startSession($cookies): void
    {
        if (!isset($cookies[session_name()])) { return; }

        if (session_status() !== PHP_SESSION_NONE) {
            throw new RuntimeException('Session started outside object context');
        }

        session_start();

        foreach ($_SESSION as $name => $value) {
            $this->session->set($name, $value);
        }

        $this->sessionContext = true;
    }

    private function closeSession(array $data): void
    {
        if (empty($data)) {
            $this->destroySession();
            return;
        }

        if (!$this->sessionContext) {
            session_start();

            $this->cookies->cookie(session_name())->value(session_id());
        }

        $_SESSION = $data;

        session_write_close();
    }

    private function destroySession(): void
    {
        if (!$this->sessionContext) { return; }

        $this->cookies->cookie(session_name())->remove();
        session_destroy();
    }
}
