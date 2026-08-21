<?php
namespace PalladiumBot;

final class Formatter
{
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function userCard(array $user, int $globalTicketNo = 0, int $userTicketNo = 0): string
    {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'نام ثبت نشده';
        $username = $user['username'] ? '@' . $user['username'] : 'ندارد';
        return "👤 <b>پرونده کاربر</b>\n".
            "• نام: " . self::e($name) . "\n".
            "• آیدی: " . self::e($username) . "\n".
            "• آیدی عددی: <code>" . self::e((string)$user['tg_id']) . "</code>\n".
            ($globalTicketNo ? "• شماره تیکت کل: <b>#{$globalTicketNo}</b>\n" : '') .
            ($userTicketNo ? "• شماره تیکت این کاربر: <b>#{$userTicketNo}</b>\n" : '');
    }

    public static function matchForm(array $form, ?array $user = null): string
    {
        $username = ($user && (int)$form['show_username'] === 1 && $user['username']) ? '@' . $user['username'] : 'نمایش داده نمی‌شود';
        return "💞 <b>فرم رل‌یابی پالادیوم</b>\n\n".
            "👤 نام: " . self::e($form['name']) . "\n".
            "🏙 شهر: " . self::e($form['city']) . "\n".
            "🌈 گرایش: " . self::e($form['orientation']) . "\n".
            "🧭 پوزیشن: " . self::e($form['position']) . "\n".
            "✨ خصوصیات خودش: " . self::e($form['self_traits']) . "\n".
            "💎 پارتنر دلخواه: " . self::e($form['partner_traits']) . "\n".
            "🆔 ارتباط: " . self::e($username) . "\n\n".
            "🇮🇷 طراحی شده توسط تیم پالادیوم";
    }

    public static function challengeResult(array $entry, array $challenge, array $user): string
    {
        return "🏆 <b>شرکت‌کننده چالش</b>\n".
            "چالش: " . self::e($challenge['title'] ?? 'چالش') . "\n".
            "ارسال‌کننده: " . self::e($user['first_name'] ?? 'کاربر') . "\n\n".
            self::e($entry['content'] ?? '') . "\n\n".
            "👍 {$entry['likes']}   👎 {$entry['dislikes']}";
    }
}
