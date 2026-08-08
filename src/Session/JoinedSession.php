<?php

declare(strict_types=1);

namespace PhpResponse\Session;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class JoinedSession implements Session
{
    private Session $primary;
    private Session $secondary;

    public function __construct(Session $primary, Session $secondary)
    {
        $this->primary = $primary;
        $this->secondary = $secondary;
    }

    public function value(Text|string $key, Text $default): Text
    {
        $fallback = new Literal("__NOT_FOUND__");
        $val = $this->primary->value($key, $fallback);
        if ($val->string() !== "__NOT_FOUND__") {
            return $val;
        }

        return $this->secondary->value($key, $default);
    }

    public function with(Text|string $key, Text $value): Session
    {
        return new self($this->primary->with($key, $value), $this->secondary);
    }
}
