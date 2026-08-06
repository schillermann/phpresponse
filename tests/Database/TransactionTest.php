<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Database;

use ArrayObject;
use PDO;
use PhpResponse\Database\Fake;
use PhpResponse\Database\Transaction\PdoTransaction;
use PhpResponse\Database\Transaction\SqliteTransaction;
use PhpResponse\Database\Transaction\SqlTransaction;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testSqlTransaction(): void
    {
        $log = new ArrayObject();
        $db = new Fake($log);

        $tx = new SqlTransaction($db);
        $tx->start();
        $tx->commit();
        $tx->rollback();

        $this->assertSame("START TRANSACTION", $log[0]['sql']);
        $this->assertSame("COMMIT", $log[1]['sql']);
        $this->assertSame("ROLLBACK", $log[2]['sql']);
    }

    public function testSqliteTransaction(): void
    {
        $log = new ArrayObject();
        $db = new Fake($log);

        $tx = new SqliteTransaction($db);
        $tx->start();
        $tx->commit();
        $tx->rollback();

        $this->assertSame("BEGIN TRANSACTION", $log[0]['sql']);
        $this->assertSame("COMMIT", $log[1]['sql']);
        $this->assertSame("ROLLBACK", $log[2]['sql']);
    }

    public function testPdoTransaction(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $tx = new PdoTransaction($pdo);

        $tx->start();
        $this->assertTrue($pdo->inTransaction());

        $tx->commit();
        $this->assertFalse($pdo->inTransaction());
    }
}
