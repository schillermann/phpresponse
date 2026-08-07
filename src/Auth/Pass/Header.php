<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Text;
use PhpResponse\Request\Header as RequestHeader;

final class Header implements Pass
{
    private Text $header;

    public function __construct(Text|string $header = 'X-Identity')
    {
        $this->header = is_string($header) ? new RequestHeader($header) : $header;
    }

    public function identity(): Identity
    {
        try {
            $value = trim($this->header->string());
            if ($value === '') {
                return new AnonymousIdentity();
            }
            return new UserIdentity($value);
        } catch (\Throwable) {
            return new AnonymousIdentity();
        }
    }
}
