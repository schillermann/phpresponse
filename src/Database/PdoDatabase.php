<?php

declare(strict_types=1);

namespace PhpResponse\Database;

use PDO;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use RuntimeException;

final class PdoDatabase implements Database
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function outcome(Statement $statement): Outcome
    {
        $stmt = $this->pdo->prepare($statement->sql());
        $success = $stmt->execute($statement->parameters());
        if (!$success) {
            throw new RuntimeException("Query execution failed: " . implode(" ", $stmt->errorInfo()));
        }

        return new AffectedRows($stmt->rowCount());
    }

    public function rows(Statement $statement): Text
    {
        $stmt = $this->pdo->prepare($statement->sql());
        $success = $stmt->execute($statement->parameters());
        if (!$success) {
            throw new RuntimeException("Query select failed: " . implode(" ", $stmt->errorInfo()));
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $encoded = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return new LiteralText($encoded !== false ? $encoded : "[]");
    }
}
