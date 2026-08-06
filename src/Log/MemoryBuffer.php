<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class MemoryBuffer implements LogBuffer
{
    /** @var array<string> */
    private array $items;

    /**
     * @param array<string> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function push(string $entry): void
    {
        $this->items[] = $entry;
    }

    public function entries(): array
    {
        return $this->items;
    }
}
