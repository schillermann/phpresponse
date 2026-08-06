<?php

declare(strict_types=1);

namespace PhpResponse\Database\Transaction;

use PhpResponse\Database\Database;
use PhpResponse\Database\SqlQuery;
use PhpResponse\Database\Transaction;

final class SqlTransaction implements Transaction
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function start(): void
    {
        $this->db->outcome(new SqlQuery("START TRANSACTION"));
    }

    public function commit(): void
    {
        $this->db->outcome(new SqlQuery("COMMIT"));
    }

    public function rollback(): void
    {
        $this->db->outcome(new SqlQuery("ROLLBACK"));
    }
}
