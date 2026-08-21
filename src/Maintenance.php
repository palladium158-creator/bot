<?php
namespace PalladiumBot;
use ZipArchive;
final class Maintenance
{
    public static function backup(string $root): string
    {
        $file = $root.'/storage/backups/backup-'.date('Ymd-His').'.zip';
        if (!is_dir(dirname($file))) mkdir(dirname($file),0755,true);
        $zip = new ZipArchive(); $zip->open($file, ZipArchive::CREATE);
        foreach (['storage/bot.sqlite','.env'] as $p) if (is_file($root.'/'.$p)) $zip->addFile($root.'/'.$p,$p);
        $zip->close(); return $file;
    }
    public static function updateFromZip(string $root, string $zipFile): void
    {
        self::backup($root); $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) throw new \RuntimeException('فایل آپدیت معتبر نیست.');
        $zip->extractTo($root); $zip->close();
    }
}
