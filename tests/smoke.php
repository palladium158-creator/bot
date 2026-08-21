<?php
require __DIR__.'/../vendor/autoload.php';
use PalladiumBot\{Config,Database,Telegram,Bot};
$root = dirname(__DIR__);
@unlink($root.'/storage/test.sqlite');
$config = new Config($root);
$db = new Database($root.'/storage/test.sqlite');
$bot = new Bot($db, new Telegram(''), $config);
$bot->handle(['message'=>['chat'=>['id'=>1,'type'=>'private'],'from'=>['id'=>8664412818,'first_name'=>'Owner'],'text'=>'/start']]);
assert($db->one('SELECT COUNT(*) c FROM users')['c'] == 1);
echo "Smoke test passed\n";
