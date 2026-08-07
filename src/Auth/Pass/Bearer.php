<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Pass;

use PhpResponse\Text;
use PhpResponse\Request\Header as RequestHeader;

final class Bearer implements Pass
{
    private Text $header;

    public function __construct(Text|string $header = 'Authorization')
    {
        $this->header = is_string($header) ? new RequestHeader($header) : $header;
    }

    public function identity(): Identity
    {
        try {
            $value = trim($this->header->string());
            if (str_starts_with($value, 'Bearer ')) {
                $token = trim(substr($value, 7));
                if ($token !== '') {
                    return new UserIdentity($token);
                }
            }
            return new AnonymousIdentity();
        } catch (\Throwable) {
            return new AnonymousIdentity();
        }
    }
}
