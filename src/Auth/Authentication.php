<?php

declare(strict_types=1);

namespace PhpResponse\Auth;

interface Authentication
{
    public function valid(): bool;
}
