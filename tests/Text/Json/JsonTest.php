<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text\Literal;

final class JsonTest extends TestCase
{
    public function testEscapesTextString(): void
    {
        $this->assertEquals(
            '"Hello \\"world\\"!"',
            (new FromText('Hello "world"!'))->string()
        );
    }

    public function testSerializesMember(): void
    {
        $this->assertEquals(
            '"status":"ok"',
            (new Member('status', 'ok'))->string()
        );
    }

    public function testSerializesObject(): void
    {
        $this->assertEquals(
            '{"status":"ok","code":200}',
            (new ObjectValue(
                new Member('status', 'ok'),
                new Member('code', new NumberValue(200))
            ))->string()
        );
    }

    public function testSerializesArray(): void
    {
        $this->assertEquals(
            '["first","second"]',
            (new ArrayValue('first', 'second'))->string()
        );
    }

    public function testSerializesNestedComplexStructure(): void
    {
        $this->assertEquals(
            '{"data":{"items":["first","second"]}}',
            (new ObjectValue(
                new Member('data', new ObjectValue(
                    new Member('items', new ArrayValue('first', 'second'))
                ))
            ))->string()
        );
    }

    public function testTrueValue(): void
    {
        $this->assertEquals('true', (new TrueValue())->string());
    }

    public function testFalseValue(): void
    {
        $this->assertEquals('false', (new FalseValue())->string());
    }

    public function testSerializesRawTextPayload(): void
    {
        $this->assertEquals(
            '{"settings":{"theme":"dark"}}',
            (new ObjectValue(
                new Member('settings', new Literal('{"theme":"dark"}'))
            ))->string()
        );
    }
}
