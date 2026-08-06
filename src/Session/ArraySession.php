<?php

declare(strict_types=1);

namespace PhpResponse\Session;

use PhpResponse\Text;

final class ArraySession implements Session
{
    /** @var array<string, Text> */
    private array $data;

    /**
     * @param array<string, Text> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function value(Text|string $key, Text $default): Text
    {
        $name = is_string($key) ? $key : $key->string();
        return $this->data[$name] ?? $default;
    }

    public function with(Text|string $key, Text $value): Session
    {
        $name = is_string($key) ? $key : $key->string();
        $copy = $this->data;
        $copy[$name] = $value;

        return new self($copy);
    }
}
