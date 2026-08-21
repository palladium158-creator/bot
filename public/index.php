<?php
require __DIR__ . '/../vendor/autoload.php';
use PalladiumBot\{Config,Database,Telegram,Bot};
$root = dirname(__DIR__); $config = new Config($root);
$dbPath = $config->get('DB_PATH', '../storage/bot.sqlite');
if (!str_starts_with($dbPath, '/')) $dbPath = $root . '/public/' . $dbPath;
$db = new Database($dbPath);
$bot = new Bot($db, new Telegram($config->token()), $config);
$update = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$bot->handle($update);
echo 'OK';
