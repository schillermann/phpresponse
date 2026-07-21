<?php

declare(strict_types=1);

namespace PhpResponse\Response\Redirect;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class TemporaryRedirect implements Response
{
    private Text $target;

    public function __construct(Text|string $target)
    {
        $this->target = is_string($target) ? new LiteralText($target) : $target;
    }

    public function media(Media $media): Media
    {
        return $media
            ->status(307, 'Temporary Redirect')
            ->header('Location', $this->target->string());
    }
}
