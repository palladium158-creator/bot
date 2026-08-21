<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
require is_file($autoload) ? $autoload : __DIR__ . '/../src/Autoload.php';
use PalladiumBot\{Config,Database,Telegram};
$root = dirname(__DIR__); $config = new Config($root);
$dbPath = $config->get('DB_PATH', '../storage/bot.sqlite');
if (!str_starts_with($dbPath, '/')) $dbPath = __DIR__ . '/' . $dbPath;
$db = new Database($dbPath);
$db->run('INSERT OR IGNORE INTO admins(tg_id,name,role,permissions,created_at) VALUES(?,?,?,?,?)',[$config->ownerId(),'مالک','owner','{"all":true}',date('c')]);
if (isset($_GET['setWebhook'])) {
    $url = rtrim((string)$config->get('APP_URL',''), '/') . '/index.php';
    header('Content-Type: application/json'); echo json_encode((new Telegram($config->token()))->call('setWebhook',['url'=>$url]), JSON_UNESCAPED_UNICODE); exit;
}
echo '<!doctype html><meta charset="utf-8"><body dir="rtl" style="font-family:tahoma;background:linear-gradient(135deg,#101827,#1f2a44);color:white;padding:30px"><div style="max-width:760px;margin:auto;background:#ffffff12;border:1px solid #ffffff30;border-radius:24px;padding:28px"><h1>🚀 نصاب ربات پالادیوم</h1><p>✅ دیتابیس آماده شد و مالک ثبت شد.</p><ol><li>فایل <code>.env.example</code> را به <code>.env</code> تبدیل و توکن/مالک/دامنه را وارد کنید.</li><li>برای ثبت وبهوک آدرس <code>?setWebhook=1</code> را باز کنید.</li><li>در تلگرام <code>/start</code> و برای مالک <code>/admin</code> را بزنید.</li></ol><p>🇮🇷 طراحی شده توسط تیم پالادیوم؛ کاملاً بومی، ایرانی و فارسی.</p></div></body>';
