<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Response;

use ArrayObject;
use Exception;
use PhpResponse\Database\Fake;
use PhpResponse\Response\Media\Fake as FakeMedia;
use PhpResponse\Response\Media;
use PhpResponse\Response\Response;
use PhpResponse\Response\TransactionalResponse;
use PHPUnit\Framework\TestCase;

final class TransactionalResponseTest extends TestCase
{
    public function testCommitsTransactionOnSuccess(): void
    {
        $log = new ArrayObject();
        $db = new Fake($log);
        $target = new class implements Response {
            public function media(Media $media): Media
            {
                return $media->status(200, "OK");
            }
        };

        $response = new TransactionalResponse($target, $db);
        $media = $response->media(new FakeMedia());

        $this->assertContains("status: 200 OK", $media->array());
        $this->assertCount(2, $log);
        $this->assertSame("START TRANSACTION", $log[0]['sql']);
        $this->assertSame("COMMIT", $log[1]['sql']);
    }

    public function testRollsBackTransactionOnException(): void
    {
        $log = new ArrayObject();
        $db = new Fake($log);
        $target = new class implements Response {
            public function media(Media $media): Media
            {
                throw new Exception("Query failed");
            }
        };

        $response = new TransactionalResponse($target, $db);

        try {
            $response->media(new FakeMedia());
            $this->fail("Expected Exception was not thrown.");
        } catch (Exception $e) {
            $this->assertSame("Query failed", $e->getMessage());
        }

        $this->assertCount(2, $log);
        $this->assertSame("START TRANSACTION", $log[0]['sql']);
        $this->assertSame("ROLLBACK", $log[1]['sql']);
    }
}
