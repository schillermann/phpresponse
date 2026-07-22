<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Fake;

final class HeaderTest extends TestCase
{
    public function testHeaderDecorator(): void
    {
        $response = new Header(
            new Ok(new Body("Hello")),
            "Content-Type", "text/html"
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Content-Type=text/html",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }

    public function testHeaderWithNameAsTextObject(): void
    {
        $response = new Header(
            new Ok(new Body("Hello")),
            new \PhpResponse\Text\LiteralText("X-Dynamic-Name"),
            new \PhpResponse\Text\LiteralText("DynamicValue")
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: X-Dynamic-Name=DynamicValue",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }

    public function testAllowOriginHeader(): void
    {
        $response = new AllowOrigin(
            new Ok(new Body("Hello")),
            "https://example.com"
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Origin=https://example.com",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }

    public function testAllowHeadersHeader(): void
    {
        $response = new AllowHeaders(
            new Ok(new Body("Hello")),
            "X-Custom-Header"
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Headers=X-Custom-Header",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }

    public function testAllowMethodsHeader(): void
    {
        $response = new AllowMethods(
            new Ok(new Body("Hello")),
            "GET, POST"
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Methods=GET, POST",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }

    public function testAllowMethodsHeaderWithArray(): void
    {
        $response = new AllowMethods(
            new Ok(new Body("Hello")),
            ["GET", "POST", "DELETE"]
        );
        $media = $response->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "header: Access-Control-Allow-Methods=GET, POST, DELETE",
                "status: 200 OK",
                "body: Hello"
            ],
            $media->array()
        );
    }
}
