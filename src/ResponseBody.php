<?php

declare(strict_types=1);

namespace PhpResponses;

final class ResponseBody implements Response
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function media(Media $media): Media
    {
        return $media->body($this->content);
    }
}
