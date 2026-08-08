<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use ArrayObject;
use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class Fake implements Database
{
    private ArrayObject $log;
    private Text $dataset;

    public function __construct(
        ArrayObject $log = new ArrayObject(),
        Text $dataset = new Literal("[]")
    ) {
        $this->log = $log;
        $this->dataset = $dataset;
    }

    public function outcome(Statement $statement): Outcome
    {
        $this->log->append(['sql' => $statement->sql(), 'params' => $statement->parameters()]);
        return new AffectedRows(1);
    }

    public function rows(Statement $statement): Text
    {
        $this->log->append(['sql' => $statement->sql(), 'params' => $statement->parameters()]);
        return $this->dataset;
    }
}
