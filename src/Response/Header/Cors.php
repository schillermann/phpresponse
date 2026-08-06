<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class Cors implements Response
{
    private Response $origin;

    public function __construct(
        Response $origin,
        Text $allowOrigin = new LiteralText('*'),
        Text $allowHeaders = new LiteralText('Content-Type, Authorization'),
        Text $allowMethods = new LiteralText('GET, POST, PUT, DELETE, OPTIONS')
    ) {
        $this->origin = new AllowOrigin(
            new AllowHeaders(
                new AllowMethods($origin, $allowMethods),
                $allowHeaders
            ),
            $allowOrigin
        );
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
