<?php

declare(strict_types=1);

namespace PhpResponse\Response\StatusLine;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;

final class Ok implements Response
{
    private Response $origin;

    public function __construct(Response $origin)
    {
        $this->origin = new StatusLine($origin, 200, 'OK');
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
