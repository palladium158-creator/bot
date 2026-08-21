<?php
namespace PalladiumBot;

final class Telegram
{
    public function __construct(private string $token) {}
    public function call(string $method, array $params = []): array
    {
        if ($this->token === '') return ['ok'=>false,'description'=>'missing token'];
        $ch = curl_init("https://api.telegram.org/bot{$this->token}/{$method}");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>$params]);
        $raw = curl_exec($ch); curl_close($ch);
        return json_decode($raw ?: '{}', true) ?: ['ok'=>false];
    }
    public function sendMessage(int|string $chatId, string $text, array $keyboard = []): void
    {
        $p = ['chat_id'=>$chatId, 'text'=>$text, 'parse_mode'=>'HTML'];
        if ($keyboard) $p['reply_markup'] = json_encode($keyboard, JSON_UNESCAPED_UNICODE);
        $this->call('sendMessage', $p);
    }
    public function copyMessage(int|string $to, int|string $from, int $messageId, array $replyMarkup = []): void
    {
        $p = ['chat_id'=>$to, 'from_chat_id'=>$from, 'message_id'=>$messageId];
        if ($replyMarkup) $p['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        $this->call('copyMessage', $p);
    }
}
