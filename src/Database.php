<?php
namespace PalladiumBot;
use PDO;

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
