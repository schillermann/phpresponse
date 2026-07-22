<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Text;

final class JoinedText implements Text
{
    /** @var array<Text|string> */
    private array $items;
    private string $separator;

    /**
     * @param array<Text|string> $items
     */
    public function __construct(array $items, string $separator = ", ")
    {
        $this->items = $items;
        $this->separator = $separator;
    }

    public function string(): string
    {
        return implode(
            $this->separator,
            array_map(function (Text|string $t): string {
                if ($t instanceof Text) {
                    return $t->string();
                }
                return (string) $t;
            }, $this->items)
        );
    }
}
