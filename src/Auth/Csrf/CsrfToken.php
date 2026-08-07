<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Text;

interface CsrfToken extends Text
{
    public function valid(Text $candidate): bool;
}
