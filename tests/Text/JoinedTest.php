<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Text;

use PhpResponse\Text\Joined;
use PhpResponse\Text\Literal;
use PHPUnit\Framework\TestCase;

final class JoinedTest extends TestCase
{
    public function testJoinedWithTextObjects(): void
    {
        $joined = new Joined(
            new Literal(', '),
            new Literal('GET'),
            new Literal('POST'),
            new Literal('PUT')
        );
        $this->assertEquals('GET, POST, PUT', $joined->string());
    }

    public function testJoinedWithTextSeparator(): void
    {
        $joined = new Joined(
            new Literal(' | '),
            new Literal('GET'),
            new Literal('POST')
        );
        $this->assertEquals('GET | POST', $joined->string());
    }
}
