<?php

declare(strict_types=1);

namespace PhpResponse\Response;

use PhpResponse\Text;
use PhpResponse\Text\Literal;

final class Body implements Response
{
    private Text $content;

    public function __construct(Text|string $content)
    {
        $this->content = is_string($content) ? new Literal($content) : $content;
    }

    public function media(Media $media): Media
    {
        return $media->body($this->content);
    }
}
