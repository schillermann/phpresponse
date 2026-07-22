<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\JoinedText;

final class AllowMethods implements Response
{
    private Response $origin;

    /**
     * @param Text|array<Text|string>|string $allowMethods
     */
    public function __construct(Response $origin, Text|array|string $allowMethods = 'GET, POST, PUT, DELETE, OPTIONS')
    {
        $value = is_array($allowMethods) ? new JoinedText($allowMethods, ', ') : $allowMethods;
        $this->origin = new Header($origin, 'Access-Control-Allow-Methods', $value);
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
