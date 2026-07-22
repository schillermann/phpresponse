<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear superglobals to ensure test isolation
        $_SERVER = [];
        $_GET = [];
    }

    public function testHeader(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $this->assertEquals('Mozilla/5.0', (new Header('User-Agent'))->string());
        $this->assertEquals('application/json', (new Header('Content-Type'))->string());
    }

    public function testHeaderThrowsExceptionWhenMissing(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        (new Header('User-Agent'))->string();
    }

    public function testMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', (new Method())->string());
    }

    public function testMethodDefaultsToGet(): void
    {
        $this->assertEquals('GET', (new Method())->string());
    }

    public function testPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/some/path?query=1';
        $this->assertEquals('/some/path', (new Path())->string());
    }

    public function testPathDefaultsToSlash(): void
    {
        $this->assertEquals('/', (new Path())->string());
    }

    public function testProtocol(): void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/2.0';
        $this->assertEquals('HTTP/2.0', (new Protocol())->string());
    }

    public function testProtocolDefaults(): void
    {
        $this->assertEquals('HTTP/1.1', (new Protocol())->string());
    }

    public function testQueryParam(): void
    {
        $_GET['name'] = 'mario';
        $this->assertEquals('mario', (new QueryParam('name'))->string());
    }

    public function testQueryParamThrowsExceptionWhenMissing(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        (new QueryParam('name'))->string();
    }

    public function testBody(): void
    {
        $this->assertIsString((new Body())->string());
    }
}
