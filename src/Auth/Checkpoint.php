<?php

declare(strict_types=1);

namespace PhpResponse\Auth;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;

final class Checkpoint implements Response
{
    private Response $target;
    private Response $fallback;
    private Authentication $auth;

    public function __construct(Response $target, Response $fallback, Authentication $auth)
    {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->auth = $auth;
    }

    public function media(Media $media): Media
    {
        if ($this->auth->valid()) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
