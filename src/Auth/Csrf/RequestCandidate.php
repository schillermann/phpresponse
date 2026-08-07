<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Request\Header;
use PhpResponse\Request\FormParam;

final class RequestCandidate implements Text
{
    private Text $origin;

    public function __construct()
    {
        $this->origin = new CsrfCandidate(
            new Header('X-CSRF-Token'),
            new Header('X-XSRF-Token'),
            new FormParam('csrf_token'),
            new FormParam('_token'),
            new LiteralText('')
        );
    }

    public function string(): string
    {
        return $this->origin->string();
    }
}
