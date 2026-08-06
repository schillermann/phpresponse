<?php

declare(strict_types=1);

namespace PhpResponse\Log;

interface LogBuffer
{
    public function push(string $entry): void;

    /**
     * @return array<string>
     */
    public function entries(): array;
}
