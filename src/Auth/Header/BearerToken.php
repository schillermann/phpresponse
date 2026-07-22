<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Header;

use PhpResponse\Auth\Authentication;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Request\Header;

final class BearerToken implements Authentication
{
    private Text $header;
    private Text $token;

    public function __construct(Text|string $token, Text|string $header = 'Authorization')
    {
        $this->token = is_string($token) ? new LiteralText($token) : $token;
        $this->header = is_string($header) ? new Header($header) : $header;
    }

    public function valid(): bool
    {
        try {
            return $this->header->string() === 'Bearer ' . $this->token->string();
        } catch (\Throwable) {
            return false;
        }
    }
}
