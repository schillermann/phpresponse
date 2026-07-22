<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\JoinedText;

final class AllowHeaders implements Response
{
    private Response $origin;

    /**
     * @param Text|array<Text|string>|string $allowHeaders
     */
    public function __construct(Response $origin, Text|array|string $allowHeaders = 'Content-Type, Authorization')
    {
        $value = is_array($allowHeaders) ? new JoinedText($allowHeaders, ', ') : $allowHeaders;
        $this->origin = new Header($origin, 'Access-Control-Allow-Headers', $value);
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
