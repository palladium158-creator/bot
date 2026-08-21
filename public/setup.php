<?php
$root = dirname(__DIR__);
$envFile = $root . '/.env';
$autoload = $root . '/vendor/autoload.php';
require is_file($autoload) ? $autoload : $root . '/src/Autoload.php';
use PalladiumBot\{Config,Database,Telegram};

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $token = trim($_POST['bot_token'] ?? '');
    $owner = preg_replace('/\D+/', '', $_POST['owner_id'] ?? '');
    if ($appUrl && $token && $owner) {
        $env = "APP_URL={$appUrl}\nBOT_TOKEN={$token}\nOWNER_ID={$owner}\nDB_PATH=../storage/bot.sqlite\nAI_FREE_ENDPOINT=\nAI_FREE_ENABLED=0\n";
        file_put_contents($envFile, $env, LOCK_EX);
        $config = new Config($root);
        $dbPath = __DIR__ . '/' . $config->get('DB_PATH', '../storage/bot.sqlite');
        $db = new Database($dbPath);
        $db->run('INSERT OR IGNORE INTO admins(tg_id,name,role,permissions,created_at) VALUES(?,?,?,?,?)',[(int)$owner,'مالک','owner','{"all":true}',date('c')]);
        $db->run('INSERT INTO settings(key,value) VALUES(?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value',['owner_id',$owner]);
        if (!empty($_POST['set_webhook'])) {
            $result = (new Telegram($token))->call('setWebhook', ['url' => $appUrl . '/index.php']);
            $message = ($result['ok'] ?? false) ? '✅ نصب کامل شد و وبهوک ثبت شد.' : '⚠️ تنظیمات ذخیره شد اما وبهوک ثبت نشد: ' . htmlspecialchars($result['description'] ?? 'unknown');
        } else {
            $message = '✅ تنظیمات ذخیره شد و دیتابیس آماده شد.';
        }
    } else {
        $message = '❌ همه فیلدها الزامی هستند.';
    }
}
$hasEnv = is_file($envFile);
?><!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>راه‌انداز پالادیوم</title></head><body><main class="card"><div class="brand"><div class="logo">🤖</div><div><h1>راه‌انداز ۰ تا ۱۰۰ ربات پالادیوم</h1><p class="muted">تنظیم توکن، مالک، دیتابیس و وبهوک برای cPanel</p></div></div><?php if($message): ?><p class="pill"><?= $message ?></p><?php endif; ?><div class="grid"><div class="pill">وضعیت env: <b class="<?= $hasEnv?'ok':'bad' ?>"><?= $hasEnv?'ساخته شده':'نیازمند تنظیم' ?></b></div><div class="pill">PHP: <b><?= htmlspecialchars(PHP_VERSION) ?></b></div><div class="pill">برند: <b>تیم پالادیوم 🇮🇷</b></div></div><form method="post"><label>آدرس HTTPS ربات</label><input class="input" name="app_url" placeholder="https://example.com" required><label>توکن ربات تلگرام</label><input class="input" name="bot_token" placeholder="123:ABC" required><label>آیدی عددی مالک</label><input class="input" name="owner_id" placeholder="8664412818" required><label><input type="checkbox" name="set_webhook" value="1" checked> وبهوک هم ثبت شود</label><button class="btn">نصب و آماده‌سازی</button></form><p class="muted">بعد از نصب، برای امنیت می‌توانید فایل <code>setup.php</code> را حذف یا تغییر نام دهید. سپس در تلگرام <code>/start</code> و <code>/admin</code> را بزنید.</p></main></body></html>
