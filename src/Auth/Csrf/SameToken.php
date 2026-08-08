<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class SameToken implements CsrfToken
{
    private Text $expected;

    public function __construct(Text|string $expected)
    {
        $this->expected = is_string($expected) ? new Literal($expected) : $expected;
    }

    public function string(): string
    {
        return $this->expected->string();
    }

    public function valid(Text $candidate): bool
    {
        try {
            return hash_equals($this->expected->string(), $candidate->string());
        } catch (\Throwable) {
            return false;
        }
    }
}
