<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PHPUnit\Framework\TestCase;

final class TemplateVariableTest extends TestCase
{
    public function testRendersTemplateVariables(): void
    {
        $rendered = new TemplateVariable(
            new TemplateVariable(
                new Literal('Hello, ${name}! You are ${age} years old.'),
                'name',
                new Literal('Yegor')
            ),
            'age',
            new Literal('40')
        );

        $this->assertEquals('Hello, Yegor! You are 40 years old.', $rendered->string());
    }
}
