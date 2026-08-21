<?php
$root = dirname(__DIR__);
$out = $argv[1] ?? dirname($root) . '/palladium-bot-cpanel-final.zip';
$zip = new ZipArchive();
if ($zip->open($out, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Cannot create {$out}\n"); exit(1);
}
$exclude = ['/.git/', '/.env', '/storage/bot.sqlite', '/storage/test.sqlite'];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    $path = $file->getPathname();
    $rel = substr($path, strlen($root) + 1);
    $normalized = '/' . str_replace('\\', '/', $rel);
    $skip = false;
    foreach ($exclude as $x) if (str_starts_with($normalized, $x) || $normalized === $x) $skip = true;
    if ($skip || str_ends_with($rel, '.zip')) continue;
    if ($file->isFile()) $zip->addFile($path, $rel);
}
$zip->close();
echo "Package built: {$out}\n";
