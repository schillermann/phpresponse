<?php

declare(strict_types=1);

namespace PhpResponses;

interface Response
{
    public function media(Media $media): Media;
}
