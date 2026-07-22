<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Header;

use PHPUnit\Framework\TestCase;

final class ApiKeyTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_API_KEY']);
    }

    public function testApiKeySuccess(): void
    {
        $_SERVER['HTTP_X_API_KEY'] = 'secret-123';
        $auth = new ApiKey('secret-123', 'X-Api-Key');
        $this->assertTrue($auth->valid());
    }

    public function testApiKeyFailure(): void
    {
        $_SERVER['HTTP_X_API_KEY'] = 'wrong-key';
        $auth = new ApiKey('secret-123', 'X-Api-Key');
        $this->assertFalse($auth->valid());
    }

    public function testApiKeyMissingHeader(): void
    {
        $auth = new ApiKey('secret-123', 'X-Api-Key');
        $this->assertFalse($auth->valid());
    }
}
