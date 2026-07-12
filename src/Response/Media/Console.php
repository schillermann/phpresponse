<?php

declare(strict_types=1);

namespace PhpResponse\Response\Media;

use PhpResponse\Response\Media;
use PhpResponse\Text;

final class Console implements Media
{
    public function status(int $code, string $message): Media
    {
        echo "Status: {$code} {$message}\n";
        return new self();
    }

    public function header(string $name, string $value): Media
    {
        echo "{$name}: {$value}\n";
        return new self();
    }

    public function body(Text $content): Media
    {
        echo "\n{$content->string()}";
        return new self();
    }
}
