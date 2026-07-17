<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;

final class FileLog implements Log
{
    private Text $path;

    public function __construct(Text $path)
    {
        $this->path = $path;
    }

    public function write(LogEntry $entry): void
    {
        file_put_contents(
            $this->path->string(),
            sprintf(
                "[%s] %s\n",
                $entry->level()->string(),
                $entry->message()->string()
            ),
            FILE_APPEND | LOCK_EX
        );
    }
}
