<?php

/*
 * This file is part of Polymorphine/User package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\User\Cookies;

use DateTime;
use DateInterval;
use Exception;


class Cookie
{
    private $name;
    private $value;
    private $minutes;
    private $options;

    public function __construct(string $name, string $value, int $minutes = 0, array $options = [])
    {
        $this->name    = $name;
        $this->value   = $value;
        $this->minutes = $minutes;

        $this->options['domain'] = $options['domain'] ?? null;
        $this->options['path']   = $options['path'] ?? '/';
        $this->options['secure'] = $options['secure'] ?? false;
        $this->options['http']   = $options['http'] ?? false;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expires(): int
    {
        return $this->minutes;
    }

    public function __toString()
    {
        $header = $this->name . '=' . $this->value;

        if ($this->options['domain']) {
            $header .= '; Domain=' . (string) $this->options['Domain'];
        }

        if ($this->minutes) {
            $date = new DateTime();

            try {
                $expire = $date->add(new DateInterval('PT' . $this->minutes . 'M'))->format(DateTime::COOKIE);
                $header .= '; Expires=' . $expire;
            } catch (Exception $e) {
            }

            $header .= '; MaxAge=' . ($this->minutes * 60);
        }

        if ($this->options['secure']) {
            $header .= '; Secure';
        }

        if ($this->options['http']) {
            $header .= '; HttpOnly';
        }

        return $header;
    }
}
