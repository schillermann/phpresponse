<?php

declare(strict_types=1);

namespace PhpResponse\Database;

interface Transaction
{
    public function start(): void;

    public function commit(): void;

    public function rollback(): void;
}
