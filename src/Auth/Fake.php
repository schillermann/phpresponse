<?php

declare(strict_types=1);

namespace PhpResponse\Auth;

final class Fake implements Authentication
{
    private bool $status;

    public function __construct(bool $status = true)
    {
        $this->status = $status;
    }

    public function valid(): bool
    {
        return $this->status;
    }
}
