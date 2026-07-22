<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text;

final class StickyTextTest extends TestCase
{
    public function testCachesOriginValue(): void
    {
        $origin = $this->createMock(Text::class);
        $origin->expects($this->once())
            ->method('string')
            ->willReturn('hello');

        $sticky = new StickyText($origin);

        $this->assertSame('hello', $sticky->string());
        $this->assertSame('hello', $sticky->string());
    }
}
