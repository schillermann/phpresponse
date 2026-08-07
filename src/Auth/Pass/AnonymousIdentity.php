<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class AnonymousIdentity implements Identity
{
    private Text $name;

    public function __construct(Text|string $name = 'anonymous')
    {
        $this->name = is_string($name) ? new LiteralText($name) : $name;
    }

    public function name(): Text
    {
        return $this->name;
    }

    public function authenticated(): bool
    {
        return false;
    }
}
