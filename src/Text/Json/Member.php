<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class Member implements Text
{
    private string $key;
    private Text $value;

    public function __construct(string $key, Json|Text|string $value)
    {
        $this->key = $key;
        $this->value = is_string($value) ? new FromText(new Literal($value)) : $value;
    }

    public function string(): string
    {
        return json_encode($this->key, JSON_THROW_ON_ERROR) . ':' . $this->value->string();
    }
}
