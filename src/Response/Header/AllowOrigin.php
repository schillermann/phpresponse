<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class AllowOrigin implements Response
{
    private Response $origin;

    public function __construct(Response $origin, Text $allowOrigin = new LiteralText('*'))
    {
        $this->origin = new Header($origin, 'Access-Control-Allow-Origin', $allowOrigin);
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
