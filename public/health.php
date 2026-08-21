<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
require is_file($autoload) ? $autoload : __DIR__ . '/../src/Autoload.php';
use PalladiumBot\{Config,Database};
$root = dirname(__DIR__);
$config = new Config($root);
$dbPath = $config->get('DB_PATH', '../storage/bot.sqlite');
if (!str_starts_with($dbPath, '/')) $dbPath = __DIR__ . '/' . $dbPath;
header('Content-Type: application/json; charset=utf-8');
try {
    $db = new Database($dbPath);
    echo json_encode(['ok'=>true,'php'=>PHP_VERSION,'db'=>is_file($dbPath),'time'=>date('c')], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
