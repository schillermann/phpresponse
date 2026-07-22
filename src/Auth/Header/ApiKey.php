<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Header;

use PhpResponse\Auth\Authentication;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Request\Header;

final class ApiKey implements Authentication
{
    private Text $header;
    private Text $expected;

    public function __construct(Text|string $expected, Text|string $header = 'X-Api-Key')
    {
        $this->expected = is_string($expected) ? new LiteralText($expected) : $expected;
        $this->header = is_string($header) ? new Header($header) : $header;
    }

    public function valid(): bool
    {
        try {
            return $this->header->string() === $this->expected->string();
        } catch (\Throwable) {
            return false;
        }
    }
}
