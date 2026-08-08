<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PHPUnit\Framework\TestCase;

final class OfFileTest extends TestCase
{
    public function testReadsFileContent(): void
    {
        $path = __DIR__ . '/test_temp_file.txt';
        file_put_contents($path, 'hello world from file');

        try {
            $this->assertEquals('hello world from file', (new OfFile($path))->string());
        } finally {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function testThrowsExceptionWhenFileNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new OfFile('non-existent-file.html'))->string();
    }
}
