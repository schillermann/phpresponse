<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PHPUnit\Framework\TestCase;

final class JoinedTextTest extends TestCase
{
    public function testJoinedTextWithStrings(): void
    {
        $joined = new JoinedText(['GET', 'POST', 'PUT'], ', ');
        $this->assertEquals('GET, POST, PUT', $joined->string());
    }

    public function testJoinedTextWithTextObjects(): void
    {
        $joined = new JoinedText([
            new LiteralText('GET'),
            new LiteralText('POST')
        ], ' | ');
        $this->assertEquals('GET | POST', $joined->string());
    }
}
