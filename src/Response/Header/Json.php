<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;

final class Json implements Response
{
    private Response $origin;

    public function __construct(Response $origin)
    {
        $this->origin = new Header($origin, 'Content-Type', 'application/json');
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
