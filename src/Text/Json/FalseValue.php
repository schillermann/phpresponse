<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

final class FalseValue implements Json
{
    public function string(): string
    {
        return 'false';
    }
}
