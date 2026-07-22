<?php

declare(strict_types=1);

namespace PhpResponse\Response\Header;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class Header implements Response
{
    private Response $origin;
    private Text $name;
    private Text $value;

    public function __construct(Response $origin, Text|string $name, Text|string $value)
    {
        $this->origin = $origin;
        $this->name = is_string($name) ? new LiteralText($name) : $name;
        $this->value = is_string($value) ? new LiteralText($value) : $value;
    }

    public function media(Media $media): Media
    {
        return $this->origin->media(
            $media->header($this->name->string(), $this->value->string())
        );
    }
}
