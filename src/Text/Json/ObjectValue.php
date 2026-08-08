<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;

final class ObjectValue implements Json
{
    /** @var array<Member> */
    private array $members;

    public function __construct(Member ...$members)
    {
        $this->members = $members;
    }

    public function string(): string
    {
        return '{' . implode(',', array_map(fn(Member $m) => $m->string(), $this->members)) . '}';
    }
}
