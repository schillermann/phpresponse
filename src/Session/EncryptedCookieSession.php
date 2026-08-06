<?php

declare(strict_types=1);

namespace PhpResponse\Session;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class EncryptedCookieSession implements Session
{
    private Text $cookieName;
    private Text $secretKey;

    /** @var array<string, string> */
    private array $payload;

    /**
     * @param array<string, string> $payload
     */
    public function __construct(Text|string $cookieName, Text|string $secretKey, array $payload = [])
    {
        $this->cookieName = is_string($cookieName) ? new LiteralText($cookieName) : $cookieName;
        $this->secretKey = is_string($secretKey) ? new LiteralText($secretKey) : $secretKey;
        $this->payload = $payload;
    }

    public function value(Text|string $key, Text $default): Text
    {
        $name = is_string($key) ? $key : $key->string();
        if (!isset($this->payload[$name])) {
            return $default;
        }

        return new LiteralText($this->payload[$name]);
    }

    public function with(Text|string $key, Text $value): Session
    {
        $name = is_string($key) ? $key : $key->string();
        $copy = $this->payload;
        $copy[$name] = $value->string();

        return new self($this->cookieName, $this->secretKey, $copy);
    }
}
