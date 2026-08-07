<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

interface Pass
{
    public function identity(): Identity;
}
