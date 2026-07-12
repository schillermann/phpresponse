<?php

declare(strict_types=1);

namespace PhpResponse\Response;

interface Response
{
    public function media(Media $media): Media;
}
