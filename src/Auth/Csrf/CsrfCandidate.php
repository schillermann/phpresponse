<?php

declare(strict_types=1);

namespace PhpResponse\Auth\Csrf;

use PhpResponse\Text;

final class CsrfCandidate implements Text
{
    /** @var array<Text> */
    private array $sources;

    public function __construct(Text ...$sources)
    {
        $this->sources = $sources;
    }

    public function string(): string
    {
        foreach ($this->sources as $source) {
            try {
                $val = trim($source->string());
                if ($val !== '') {
                    return $val;
                }
            } catch (\Throwable) {
                // Ignore missing parameters/headers
            }
        }
        return '';
    }
}
