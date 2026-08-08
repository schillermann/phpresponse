<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Text;

final class Joined implements Text
{
    private Text $separator;

    /** @var array<Text> */
    private array $items;

    public function __construct(Text $separator, Text ...$items)
    {
        $this->separator = $separator;
        $this->items = $items;
    }

    public function string(): string
    {
        return implode(
            $this->separator->string(),
            array_map(fn(Text $t): string => $t->string(), $this->items)
        );
    }
}
