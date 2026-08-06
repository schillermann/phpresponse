<?php

declare(strict_types=1);

namespace PhpResponse\Database\Transaction;

use PDO;
use PhpResponse\Database\Transaction;

final class PdoTransaction implements Transaction
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function start(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
