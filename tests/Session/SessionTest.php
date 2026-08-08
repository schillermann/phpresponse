<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Session;

use PhpResponse\Session\ArraySession;
use PhpResponse\Session\EncryptedCookieSession;
use PhpResponse\Text\Literal;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testArraySessionStoresAndRetrievesImmutablyWithFallback(): void
    {
        $session = new ArraySession();
        $key = new Literal('user_id');
        $fallback = new Literal('guest');

        $this->assertSame('guest', $session->value($key, $fallback)->string());

        $updated = $session->with($key, new Literal('42'));
        $this->assertSame('guest', $session->value($key, $fallback)->string());
        $this->assertSame('42', $updated->value($key, $fallback)->string());
    }

    public function testEncryptedCookieSessionStoresImmutablyWithFallback(): void
    {
        $session = new EncryptedCookieSession(
            new Literal('app_session'),
            new Literal('secret123')
        );
        $key = new Literal('role');
        $fallback = new Literal('guest');

        $this->assertSame('guest', $session->value($key, $fallback)->string());

        $updated = $session->with($key, new Literal('admin'));
        $this->assertSame('guest', $session->value($key, $fallback)->string());
        $this->assertSame('admin', $updated->value($key, $fallback)->string());
    }
}
