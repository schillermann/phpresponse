<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use ArrayObject;

final class BufferLog implements Log
{
    private LogBuffer $buffer;

    public function __construct(LogBuffer|ArrayObject $buffer)
    {
        if ($buffer instanceof ArrayObject) {
            $this->buffer = new class($buffer) implements LogBuffer {
                private ArrayObject $array;
                public function __construct(ArrayObject $array) { $this->array = $array; }
                public function push(string $entry): void { $this->array->append($entry); }
                public function entries(): array { return $this->array->getArrayCopy(); }
            };
        } else {
            $this->buffer = $buffer;
        }
    }

    public function write(LogEntry $entry): void
    {
        $this->buffer->push(
            sprintf(
                '%s: %s',
                $entry->level()->string(),
                $entry->message()->string()
            )
        );
    }
}
