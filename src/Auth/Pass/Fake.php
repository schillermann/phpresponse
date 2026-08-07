<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

final class Fake implements Pass
{
    private Identity $identity;

    public function __construct(Identity $identity = new AnonymousIdentity())
    {
        $this->identity = $identity;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }
}
