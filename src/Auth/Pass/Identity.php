<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Text;

interface Identity
{
    public function name(): Text;
    public function authenticated(): bool;
}
