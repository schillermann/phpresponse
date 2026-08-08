<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Response\Header;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\Header\Header;
use PhpResponse\Response\Header\AllowHeaders;
use PhpResponse\Response\Header\AllowMethods;
use PhpResponse\Response\Header\AllowOrigin;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Fake;
use PhpResponse\Text\Literal;
use PhpResponse\Text\Joined;

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
            new \PhpResponse\Text\Literal("X-Dynamic-Name"),
            new \PhpResponse\Text\Literal("DynamicValue")
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
            new Literal("https://example.com")
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
            new Literal("X-Custom-Header")
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
            new Literal("GET, POST")
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

    public function testAllowMethodsHeaderWithJoined(): void
    {
        $response = new AllowMethods(
            new Ok(new Body("Hello")),
            new Joined(
                new Literal(", "),
                new Literal("GET"),
                new Literal("POST"),
                new Literal("DELETE")
            )
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
