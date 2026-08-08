<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\Text\Literal;
use Throwable;

final class ExceptionEntry implements LogEntry
{
    private Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function level(): Text
    {
        return new Literal("ERROR");
    }

    public function message(): Text
    {
        return new Literal(
            $this->exception->getMessage() . "\n" . $this->exception->getTraceAsString()
        );
    }
}
