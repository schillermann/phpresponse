<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class AffectedRows implements Outcome
{
    private int $affectedRows;
    private Text $payload;

    public function __construct(int $affectedRows = 0, Text $payload = new LiteralText(""))
    {
        $this->affectedRows = $affectedRows;
        $this->payload = $payload;
    }

    public function size(): int
    {
        return $this->affectedRows;
    }

    public function text(): Text
    {
        return $this->payload;
    }
}
