<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Text;
use PhpResponse\Request\Cookie as RequestCookie;

final class Cookie implements Pass
{
    private Text $cookie;

    public function __construct(Text|string $cookie = 'ps_identity')
    {
        $this->cookie = is_string($cookie) ? new RequestCookie($cookie) : $cookie;
    }

    public function identity(): Identity
    {
        try {
            $value = trim($this->cookie->string());
            if ($value === '') {
                return new AnonymousIdentity();
            }
            return new UserIdentity($value);
        } catch (\Throwable) {
            return new AnonymousIdentity();
        }
    }
}
