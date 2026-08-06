<?php

declare(strict_types=1);

namespace PhpResponse\Database;

final class SqlQuery implements Statement
{
    private string $sql;

    /** @var array<string|int, mixed> */
    private array $params;

    /**
     * @param array<string|int, mixed> $params
     */
    public function __construct(string $sql, array $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function sql(): string
    {
        return $this->sql;
    }

    public function parameters(): array
    {
        return $this->params;
    }
}
