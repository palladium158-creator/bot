<?php
namespace PalladiumBot;
final class Keyboards
{
    public static function main(): array { return ['inline_keyboard'=>[
        [['text'=>'🎫 تیکت پشتیبانی','callback_data'=>'svc:ticket'],['text'=>'🏆 چالش‌ها','callback_data'=>'svc:challenge']],
        [['text'=>'💞 رل‌یابی','callback_data'=>'svc:match'],['text'=>'ℹ️ راهنما','callback_data'=>'help']],
    ]]; }
    public static function admin(): array { return ['inline_keyboard'=>[
        [['text'=>'📊 آمار کل','callback_data'=>'admin:stats'],['text'=>'🎫 تیکت‌ها','callback_data'=>'admin:tickets']],
        [['text'=>'🏆 مدیریت چالش','callback_data'=>'admin:challenges'],['text'=>'💞 فرم‌های رل‌یابی','callback_data'=>'admin:matches']],
        [['text'=>'📣 ارسال پیام','callback_data'=>'admin:broadcast'],['text'=>'👮 مدیران','callback_data'=>'admin:admins']],
        [['text'=>'🔐 عضویت اجباری','callback_data'=>'admin:force'],['text'=>'📝 متن‌ها','callback_data'=>'admin:texts']],
        [['text'=>'💾 بکاپ/آپدیت','callback_data'=>'admin:update']],
    ]]; }
    public static function back(): array { return ['inline_keyboard'=>[[['text'=>'🔙 بازگشت','callback_data'=>'home']]]]; }
}
