<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text\LiteralText;

final class JsonTextTest extends TestCase
{
    public function testEscapesTextString(): void
    {
        $this->assertEquals(
            '"Hello \\"world\\"!"',
            (new JsonText('Hello "world"!'))->string()
        );
    }

    public function testSerializesMember(): void
    {
        $this->assertEquals(
            '"status":"ok"',
            (new JsonMember('status', 'ok'))->string()
        );
    }

    public function testSerializesObject(): void
    {
        $this->assertEquals(
            '{"status":"ok","code":200}',
            (new JsonObject(
                new JsonMember('status', 'ok'),
                new JsonMember('code', new JsonNumber(200))
            ))->string()
        );
    }

    public function testSerializesArray(): void
    {
        $this->assertEquals(
            '["first","second"]',
            (new JsonArray('first', 'second'))->string()
        );
    }

    public function testSerializesNestedComplexStructure(): void
    {
        $this->assertEquals(
            '{"data":{"items":["first","second"]}}',
            (new JsonObject(
                new JsonMember('data', new JsonObject(
                    new JsonMember('items', new JsonArray('first', 'second'))
                ))
            ))->string()
        );
    }

    public function testJsonTrue(): void
    {
        $this->assertEquals('true', (new JsonTrue())->string());
    }

    public function testJsonFalse(): void
    {
        $this->assertEquals('false', (new JsonFalse())->string());
    }

    public function testSerializesRawTextPayload(): void
    {
        $this->assertEquals(
            '{"settings":{"theme":"dark"}}',
            (new JsonObject(
                new JsonMember('settings', new LiteralText('{"theme":"dark"}'))
            ))->string()
        );
    }
}
