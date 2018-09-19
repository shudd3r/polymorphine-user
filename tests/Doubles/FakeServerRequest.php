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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;


class FakeServerRequest implements ServerRequestInterface
{
    public $uri;
    public $method;
    public $attr    = [];
    public $cookies = [];
    public $parsed  = [];

    public function __construct(string $method = 'GET', UriInterface $uri = null)
    {
        $this->method = $method;
        $this->uri    = $uri;
    }

    public function getMethod()
    {
        return $this->method ?: 'GET';
    }

    public function getUri()
    {
        return $this->uri ?: FakeUri::fromString('//example.com/foo/bar');
    }

    public function getRequestTarget()
    {
        $query = $this->getUri()->getquery();
        $path  = $this->getUri()->getPath();

        return $query ? $path . '?' . $query : $path;
    }

    public function getProtocolVersion()
    {
    }

    public function withProtocolVersion($version)
    {
    }

    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
    }

    public function getHeaderLine($name)
    {
    }

    public function withHeader($name, $value)
    {
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
    }

    public function withBody(StreamInterface $body)
    {
    }

    public function withRequestTarget($requestTarget)
    {
    }

    public function withMethod($method)
    {
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
    }

    public function getServerParams()
    {
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies)
    {
    }

    public function getQueryParams()
    {
    }

    public function withQueryParams(array $query)
    {
    }

    public function getUploadedFiles()
    {
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
    }

    public function getParsedBody()
    {
        return $this->parsed;
    }

    public function withParsedBody($data)
    {
    }

    public function getAttributes()
    {
        return $this->attr;
    }

    public function getAttribute($name, $default = null)
    {
        return $this->attr[$name] ?? $default;
    }

    public function withAttribute($name, $value)
    {
        $this->attr[$name] = $value;
        return $this;
    }

    public function withoutAttribute($name)
    {
    }
}
