<?php

declare(strict_types=1);

namespace PhpResponse\Route;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text\Literal;
use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Media\Fake;

final class RoutingTest extends TestCase
{
    public function testRoutesCorrectlyOnMatch(): void
    {
        $media = (new Method(
            "GET",
            new RegexPath(
                "#^/home$#",
                new Ok(new Body(new Literal("Home page"))),
                new Body(new Literal("Unreached Path Fallback")),
                new Literal("/home")
            ),
            new Body(new Literal("Method Fallback")),
            new Literal("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: Home page"
            ],
            $media->array()
        );
    }

    public function testRoutesCorrectlyOnExactPathMatch(): void
    {
        $media = (new Method(
            "GET",
            new ExactPath(
                "/about",
                new Ok(new Body(new Literal("About page"))),
                new Body(new Literal("404")),
                new Literal("/about")
            ),
            new Body(new Literal("Method Fallback")),
            new Literal("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: About page"
            ],
            $media->array()
        );
    }

    public function testReturnsFallbackWhenNoMatch(): void
    {
        $media = (new Method(
            "POST",
            new RegexPath(
                "#^/home$#",
                new Ok(new Body(new Literal("Home"))),
                new Body(new Literal("Regex Fallback")),
                new Literal("/home")
            ),
            new Body(new Literal("Method Fallback")),
            new Literal("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "body: Method Fallback"
            ],
            $media->array()
        );
    }
}
