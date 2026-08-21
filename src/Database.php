<?php
namespace PalladiumBot;
use PDO;
use Throwable;

final class Database
{
    public PDO $pdo;
    public function __construct(string $path)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->migrate();
    }

    public function migrate(): void
    {
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        $this->pdo->exec($sql);
        $this->safeAlter('admins', 'warning_limit', 'INTEGER DEFAULT 3');
        $this->safeAlter('match_forms', 'channel_id', 'TEXT');
        $this->safeAlter('match_forms', 'channel_message_id', 'INTEGER');
        $this->safeAlter('challenge_entries', 'likes', 'INTEGER DEFAULT 0');
        $this->safeAlter('challenge_entries', 'dislikes', 'INTEGER DEFAULT 0');
        $this->run('INSERT OR IGNORE INTO settings(key,value) VALUES(?,?)', ['schema_version', '2']);
    }

    private function safeAlter(string $table, string $column, string $definition): void
    {
        try { $this->pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}"); } catch (Throwable) {}
    }

    public function one(string $sql, array $params = []): ?array
    {
        $s = $this->pdo->prepare($sql); $s->execute($params);
        $r = $s->fetch(PDO::FETCH_ASSOC); return $r ?: null;
    }
    public function all(string $sql, array $params = []): array
    {
        $s = $this->pdo->prepare($sql); $s->execute($params);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }
    public function run(string $sql, array $params = []): void
    {
        $s = $this->pdo->prepare($sql); $s->execute($params);
    }
}
