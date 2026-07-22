<?php

declare(strict_types=1);

namespace PhpResponse\Auth;

use PHPUnit\Framework\TestCase;

final class FakeTest extends TestCase
{
    public function testFakeAuth(): void
    {
        $this->assertTrue((new Fake(true))->valid());
        $this->assertFalse((new Fake(false))->valid());
    }
}
