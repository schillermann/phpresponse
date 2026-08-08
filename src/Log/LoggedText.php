<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\Text\Literal;
use PhpResponse\Text\Formatted;
use PhpResponse\Log\Level\DebugLevel;

final class LoggedText implements Text
{
    private Text $origin;
    private Log $log;

    public function __construct(Text $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function string(): string
    {
        $result = $this->origin->string();
        $this->log->write(
            new PlainEntry(
                new DebugLevel(),
                new Formatted(
                    new Literal("Evaluated text: '%s'"),
                    new Literal($result)
                )
            )
        );
        return $result;
    }
}
