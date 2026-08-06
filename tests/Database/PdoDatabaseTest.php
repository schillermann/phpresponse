<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Database;

use PDO;
use PhpResponse\Database\PdoDatabase;
use PhpResponse\Database\SqlQuery;
use PHPUnit\Framework\TestCase;

final class PdoDatabaseTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
    }

    public function testOutcomeAndRowsWithPdo(): void
    {
        $db = new PdoDatabase($this->pdo);

        $outcome = $db->outcome(new SqlQuery("INSERT INTO users (name) VALUES (:name)", ['name' => 'Alice']));
        $this->assertSame(1, $outcome->size());

        $text = $db->rows(new SqlQuery("SELECT name FROM users WHERE id = :id", ['id' => 1]));
        $this->assertSame('[{"name":"Alice"}]', $text->string());
    }
}
