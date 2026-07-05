<?php

declare(strict_types=1);

namespace PhpResponses;

use PHPUnit\Framework\TestCase;

final class TemplateTest extends TestCase
{
    public function testRendersTemplateVariables(): void
    {
        $template = new LiteralText('Hello, ${name}! You are ${age} years old.');
        $rendered = new Template($template, [
            'name' => new LiteralText('Yegor'),
            'age' => new LiteralText('40')
        ]);

        $this->assertEquals('Hello, Yegor! You are 40 years old.', $rendered->string());
    }
}
