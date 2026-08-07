<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Response\StatusLine\Forbidden;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Request\Method;

final class GuardedResponse implements Response
{
    private Response $target;
    private Response $fallback;
    private CsrfToken $token;
    private Text $candidate;
    private Text $method;

    public function __construct(
        Response $target,
        CsrfToken $token,
        Response $fallback = new Forbidden(
            new Header(
                new Body(new LiteralText("CSRF token verification failed.")),
                "Content-Type",
                "text/plain"
            )
        ),
        Text $candidate = new RequestCandidate(),
        Text $method = new Method()
    ) {
        $this->target = $target;
        $this->token = $token;
        $this->fallback = $fallback;
        $this->candidate = $candidate;
        $this->method = $method;
    }

    public function media(Media $media): Media
    {
        $verb = strtoupper(trim($this->method->string()));
        if (in_array($verb, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $this->target->media($media);
        }

        if ($this->token->valid($this->candidate)) {
            return $this->target->media($media);
        }

        return $this->fallback->media($media);
    }
}
