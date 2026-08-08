<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

/**
 * @deprecated Use PhpResponse\Database\AffectedRows instead.
 */
final class ExecResult implements QueryResult
{
    private AffectedRows $origin;

    public function __construct(int $affectedRows = 0, Text $payload = new Literal(""))
    {
        $this->origin = new AffectedRows($affectedRows, $payload);
    }

    public function count(): int
    {
        return $this->origin->size();
    }

    public function size(): int
    {
        return $this->origin->size();
    }

    public function text(): Text
    {
        return $this->origin->text();
    }
}
