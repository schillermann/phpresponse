<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Text;

final class RandomToken implements CsrfToken
{
    private int $bytes;
    private SameToken $delegate;

    public function __construct(int $bytes = 32)
    {
        $this->bytes = $bytes;
        $this->delegate = new SameToken(bin2hex(random_bytes($bytes)));
    }

    public function string(): string
    {
        return $this->delegate->string();
    }

    public function valid(Text $candidate): bool
    {
        return $this->delegate->valid($candidate);
    }
}
