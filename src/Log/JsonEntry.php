<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class JsonEntry implements LogEntry
{
    private LogEntry $origin;

    public function __construct(LogEntry $origin)
    {
        $this->origin = $origin;
    }

    public function level(): Text
    {
        return $this->origin->level();
    }

    public function message(): Text
    {
        return new Literal(
            (string) json_encode([
                'level' => $this->origin->level()->string(),
                'message' => $this->origin->message()->string()
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
