<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Auth\Authentication;

final class IdentityAuthentication implements Authentication
{
    private Pass $pass;

    public function __construct(Pass $pass)
    {
        $this->pass = $pass;
    }

    public function valid(): bool
    {
        return $this->pass->identity()->authenticated();
    }
}
