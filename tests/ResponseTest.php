<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\Body;
use PhpResponse\Response\Header;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Media\Fake;

final class ResponseTest extends TestCase
{
    public function testFullResponse(): void
    {
        $media = (new Ok(
            new Header(
                new Body(new LiteralText("Hello!")),
                "X-Custom", "Value"
            )
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "header: X-Custom=Value",
                "body: Hello!"
            ],
            $media->array()
        );
    }
}
