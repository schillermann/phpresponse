<?php

declare(strict_types=1);

namespace PhpResponse\Session;

use PhpResponse\Text;

interface Session
{
    public function value(Text|string $key, Text $default): Text;

    public function with(Text|string $key, Text $value): Session;
}
