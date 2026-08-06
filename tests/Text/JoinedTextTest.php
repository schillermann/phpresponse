<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Text;

use PhpResponse\Text\JoinedText;
use PhpResponse\Text\LiteralText;
use PHPUnit\Framework\TestCase;

final class JoinedTextTest extends TestCase
{
    public function testJoinedTextWithTextObjects(): void
    {
        $joined = new JoinedText(
            new LiteralText(', '),
            new LiteralText('GET'),
            new LiteralText('POST'),
            new LiteralText('PUT')
        );
        $this->assertEquals('GET, POST, PUT', $joined->string());
    }

    public function testJoinedTextWithTextSeparator(): void
    {
        $joined = new JoinedText(
            new LiteralText(' | '),
            new LiteralText('GET'),
            new LiteralText('POST')
        );
        $this->assertEquals('GET | POST', $joined->string());
    }
}
