<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class AllowMethods implements Response
{
    private Response $origin;

    public function __construct(Response $origin, Text $allowMethods = new LiteralText('GET, POST, PUT, DELETE, OPTIONS'))
    {
        $this->origin = new Header($origin, 'Access-Control-Allow-Methods', $allowMethods);
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
