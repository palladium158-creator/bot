<?php
namespace PalladiumBot;

final class AiClient
{
    public function __construct(private Database $db) {}

    public function enabled(): bool
    {
        return ($this->setting('ai_free_enabled') === '1') && $this->setting('ai_free_endpoint') !== '';
    }

    public function answer(string $prompt): ?string
    {
        if (!$this->enabled()) return null;
        $endpoint = $this->setting('ai_free_endpoint');
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['prompt' => $prompt, 'lang' => 'fa'], JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 8,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($raw ?: '{}', true);
        return $json['answer'] ?? $json['text'] ?? null;
    }

    private function setting(string $key): string
    {
        return $this->db->one('SELECT value FROM settings WHERE key=?', [$key])['value'] ?? '';
    }
}
