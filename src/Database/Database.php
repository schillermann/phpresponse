<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use PhpResponse\Text;

interface Database
{
    public function outcome(Statement $statement): Outcome;

    public function rows(Statement $statement): Text;
}
