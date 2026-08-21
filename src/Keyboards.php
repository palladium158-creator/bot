<?php
namespace PalladiumBot;
final class Keyboards
{
    public static function main(): array { return ['inline_keyboard'=>[
        [['text'=>'🎫 تیکت پشتیبانی','callback_data'=>'svc:ticket'],['text'=>'🏆 چالش‌ها','callback_data'=>'svc:challenge']],
        [['text'=>'💞 فرم رل‌یابی','callback_data'=>'svc:match'],['text'=>'🤖 رل‌یابی با ربات','callback_data'=>'svc:matchbot']],
        [['text'=>'ℹ️ راهنما','callback_data'=>'help']],
    ]]; }
    public static function admin(): array { return ['inline_keyboard'=>[
        [['text'=>'📊 آمار کل','callback_data'=>'admin:stats'],['text'=>'🎫 تیکت‌ها','callback_data'=>'admin:tickets']],
        [['text'=>'🏆 چالش‌ها','callback_data'=>'admin:challenges'],['text'=>'💞 رل‌یابی','callback_data'=>'admin:matches']],
        [['text'=>'📣 ارسال همگانی','callback_data'=>'admin:broadcast'],['text'=>'📌 ارسال/پین کانال','callback_data'=>'admin:post']],
        [['text'=>'👮 مدیران و اخطار','callback_data'=>'admin:admins'],['text'=>'🔐 عضویت اجباری','callback_data'=>'admin:force']],
        [['text'=>'📝 متن‌ها و پاسخ آماده','callback_data'=>'admin:texts'],['text'=>'💾 بکاپ/آپدیت','callback_data'=>'admin:update']],
    ]]; }
    public static function back(string $to = 'home'): array { return ['inline_keyboard'=>[[['text'=>'🔙 بازگشت','callback_data'=>$to]]]]; }
    public static function ticketAdmin(int $ticketId): array { return ['inline_keyboard'=>[
        [['text'=>'✍️ پاسخ','callback_data'=>"ticket:reply:$ticketId"],['text'=>'↪️ ارجاع','callback_data'=>"ticket:assign:$ticketId"]],
        [['text'=>'📝 یادداشت خصوصی','callback_data'=>"ticket:note:$ticketId"],['text'=>'✅ بستن','callback_data'=>"ticket:close:$ticketId"]],
    ]]; }
    public static function moderation(string $type, int $id): array { return ['inline_keyboard'=>[
        [['text'=>'✅ تأیید','callback_data'=>"mod:$type:approve:$id"],['text'=>'❌ رد','callback_data'=>"mod:$type:reject:$id"]],
    ]]; }
    public static function vote(int $entryId): array { return ['inline_keyboard'=>[[['text'=>'👍 لایک','callback_data'=>"vote:like:$entryId"],['text'=>'👎 دیس‌لایک','callback_data'=>"vote:dislike:$entryId"]]]]; }
}
