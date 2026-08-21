<?php
namespace PalladiumBot;

final class Config
{
    private array $data;

    public function __construct(string $root)
    {
        $this->data = [];
        $env = $root . '/.env';
        if (is_file($env)) {
            foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $this->data[trim($k)] = trim($v);
            }
        }
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return getenv($key) ?: ($this->data[$key] ?? $default);
    }

    public function token(): string { return (string)$this->get('BOT_TOKEN', ''); }
    public function ownerId(): int { return (int)$this->get('OWNER_ID', '0'); }
}
