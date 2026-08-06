<?php

declare(strict_types=1);

namespace PhpResponse\Database;

interface Statement
{
    public function sql(): string;

    /**
     * @return array<string|int, mixed>
     */
    public function parameters(): array;
}
