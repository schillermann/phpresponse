<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use ArrayObject;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

/**
 * @deprecated Use PhpResponse\Database\Fake instead.
 */
final class MemoryDatabase implements Database
{
    private Fake $origin;

    public function __construct(
        ArrayObject $log = new ArrayObject(),
        Text $dataset = new LiteralText("[]")
    ) {
        $this->origin = new Fake($log, $dataset);
    }

    public function outcome(Statement $statement): Outcome
    {
        return $this->origin->outcome($statement);
    }

    public function rows(Statement $statement): Text
    {
        return $this->origin->rows($statement);
    }
}
