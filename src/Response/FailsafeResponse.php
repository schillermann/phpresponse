<?php

declare(strict_types=1);

namespace PhpResponse\Response;

use PhpResponse\Log\ExceptionEntry;
use PhpResponse\Log\Log;
use PhpResponse\Response\Header\Header;
use PhpResponse\Response\StatusLine\InternalServerError;
use PhpResponse\Text\Literal;
use Throwable;

final class FailsafeResponse implements Response
{
    private Response $origin;
    private Log $log;

    public function __construct(Response $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function media(Media $media): Media
    {
        try {
            return $this->origin->media($media);
        } catch (Throwable $ex) {
            $this->log->write(new ExceptionEntry($ex));
            return (new InternalServerError(
                new Header(
                    new Body(new Literal("Something went wrong.")),
                    "Content-Type",
                    "text/plain"
                )
            ))->media($media);
        }
    }
}
