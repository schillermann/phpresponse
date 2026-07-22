<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Body;
use PhpResponse\Text\LiteralText;
use PhpResponse\Response\Media\Fake;

final class CorsTest extends TestCase
{
    public function testDefaultCorsHeaders(): void
    {
        $response = new Cors(
            new Ok(new Body(new LiteralText("CORS Content")))
        );

        $media = $response->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Origin=*",
                "header: Access-Control-Allow-Headers=Content-Type, Authorization",
                "header: Access-Control-Allow-Methods=GET, POST, PUT, DELETE, OPTIONS",
                "status: 200 OK",
                "body: CORS Content"
            ],
            $media->array()
        );
    }

    public function testCustomCorsHeaders(): void
    {
        $response = new Cors(
            new Ok(new Body(new LiteralText("Custom CORS"))),
            "https://example.com",
            "X-Custom-Header",
            "GET, POST"
        );

        $media = $response->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Origin=https://example.com",
                "header: Access-Control-Allow-Headers=X-Custom-Header",
                "header: Access-Control-Allow-Methods=GET, POST",
                "status: 200 OK",
                "body: Custom CORS"
            ],
            $media->array()
        );
    }
}
