<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Response;

use Exception;
use PhpResponse\Log\BufferLog;
use PhpResponse\Response\FailsafeResponse;
use PhpResponse\Response\Media\Fake;
use PhpResponse\Response\Media;
use PhpResponse\Response\Response;
use PHPUnit\Framework\TestCase;

final class FailsafeResponseTest extends TestCase
{
    public function testFailsafePassesThroughSuccessfulResponse(): void
    {
        $buffer = new \ArrayObject();
        $log = new BufferLog($buffer);
        $target = new class implements Response {
            public function media(Media $media): Media
            {
                return $media->status(200, "OK");
            }
        };

        $failsafe = new FailsafeResponse($target, $log);
        $media = $failsafe->media(new Fake());

        $this->assertContains("status: 200 OK", $media->array());
    }

    public function testFailsafeCatchesExceptionAndLogsIt(): void
    {
        $buffer = new \ArrayObject();
        $log = new BufferLog($buffer);
        $target = new class implements Response {
            public function media(Media $media): Media
            {
                throw new Exception("Database connection failed");
            }
        };

        $failsafe = new FailsafeResponse($target, $log);
        $media = $failsafe->media(new Fake());

        $this->assertContains("status: 500 Internal Server Error", $media->array());
        $this->assertContains("body: Something went wrong.", $media->array());

        $this->assertCount(1, $buffer);
        $this->assertStringContainsString("Database connection failed", (string) $buffer[0]);
    }
}
