<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
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
        return new LiteralText("ERROR");
    }

    public function message(): Text
    {
        return new LiteralText(
            $this->exception->getMessage() . "\n" . $this->exception->getTraceAsString()
        );
    }
}
