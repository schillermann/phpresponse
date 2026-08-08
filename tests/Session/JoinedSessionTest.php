<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Session;

use PhpResponse\Session\ArraySession;
use PhpResponse\Session\JoinedSession;
use PhpResponse\Text\Literal;
use PHPUnit\Framework\TestCase;

final class JoinedSessionTest extends TestCase
{
    public function testJoinedSessionOverridesSecondaryValues(): void
    {
        $secondary = new ArraySession([
            'role' => new Literal('user'),
            'theme' => new Literal('light'),
        ]);

        $primary = new ArraySession([
            'role' => new Literal('admin'),
        ]);

        $joined = new JoinedSession($primary, $secondary);
        $fallback = new Literal('none');

        // Role should come from primary ('admin')
        $this->assertSame('admin', $joined->value('role', $fallback)->string());

        // Theme should fall back to secondary ('light')
        $this->assertSame('light', $joined->value('theme', $fallback)->string());

        // Missing key returns fallback ('none')
        $this->assertSame('none', $joined->value('missing', $fallback)->string());
    }
}
