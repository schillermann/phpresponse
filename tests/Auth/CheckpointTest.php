<?php

declare(strict_types=1);

namespace PhpResponse\Auth;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Fake as FakeMedia;

final class CheckpointTest extends TestCase
{
    public function testCheckpointWhenAuthorized(): void
    {
        $response = new Checkpoint(
            new Ok(new Body("Secret Data")),
            new Unauthorized(new Body("Access Denied")),
            new Fake(true)
        );

        $media = $response->media(new FakeMedia());

        /** @var FakeMedia $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: Secret Data"
            ],
            $media->array()
        );
    }

    public function testCheckpointWhenUnauthorized(): void
    {
        $response = new Checkpoint(
            new Ok(new Body("Secret Data")),
            new Unauthorized(new Body("Access Denied")),
            new Fake(false)
        );

        $media = $response->media(new FakeMedia());

        /** @var FakeMedia $media */
        $this->assertEquals(
            [
                "status: 401 Unauthorized",
                "body: Access Denied"
            ],
            $media->array()
        );
    }
}
