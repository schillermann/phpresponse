<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use PhpResponse\Text;

interface Outcome
{
    public function size(): int;

    public function text(): Text;
}
