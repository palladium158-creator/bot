<?php
require __DIR__ . '/../vendor/autoload.php';
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
echo '<!doctype html><meta charset="utf-8"><body dir="rtl" style="font-family:tahoma;background:#101827;color:white;padding:30px"><h1>نصاب ربات پالادیوم</h1><p>✅ دیتابیس آماده شد و مالک ثبت شد.</p><p>برای ثبت وبهوک آدرس <code>?setWebhook=1</code> را باز کنید.</p><p>طراحی شده توسط تیم پالادیوم؛ کاملاً بومی، ایرانی و فارسی.</p></body>';
