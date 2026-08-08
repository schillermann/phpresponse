<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text;

final class FallbackTest extends TestCase
{
    public function testReturnsOriginWhenSuccessful(): void
    {
        $text = new Fallback(
            new Literal('success'),
            new Literal('fallback')
        );

        $this->assertEquals('success', $text->string());
    }

    public function testReturnsFallbackWhenExceptionThrown(): void
    {
        $origin = new class implements Text {
            public function string(): string {
                throw new \RuntimeException('error');
            }
        };

        $text = new Fallback(
            $origin,
            new Literal('fallback')
        );

        $this->assertEquals('fallback', $text->string());
    }

    public function testPropagatesUnmatchedException(): void
    {
        $origin = new class implements Text {
            public function string(): string {
                throw new \InvalidArgumentException('error');
            }
        };

        $text = new Fallback(
            $origin,
            new Literal('fallback'),
            \RuntimeException::class
        );

        $this->expectException(\InvalidArgumentException::class);
        $text->string();
    }
}
