<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class AllowHeaders implements Response
{
    private Response $origin;

    public function __construct(Response $origin, Text $allowHeaders = new Literal('Content-Type, Authorization'))
    {
        $this->origin = new Header($origin, 'Access-Control-Allow-Headers', $allowHeaders);
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
