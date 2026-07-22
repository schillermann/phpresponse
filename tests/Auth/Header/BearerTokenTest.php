<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Header;

use PHPUnit\Framework\TestCase;

final class BearerTokenTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testBearerTokenSuccess(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token-abc-456';
        $auth = new BearerToken('token-abc-456');
        $this->assertTrue($auth->valid());
    }

    public function testBearerTokenFailure(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer wrong-token';
        $auth = new BearerToken('token-abc-456');
        $this->assertFalse($auth->valid());
    }

    public function testBearerTokenMissingHeader(): void
    {
        $auth = new BearerToken('token-abc-456');
        $this->assertFalse($auth->valid());
    }
}
