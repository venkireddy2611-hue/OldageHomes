<?php
require_once __DIR__ . '/config.php';

/**
 * Simple PDO connection helper.
 * Tries to use DSN in environment for MySQL/Postgres, otherwise falls back to SQLite.
 */
function get_db() {
    static $pdo = null;
    if ($pdo) return $pdo;

    // If environment DSN is provided (e.g., DB_DSN=mysql:host=...;dbname=...), try that
    if (!empty($_ENV['DB_DSN'])) {
        $dsn = $_ENV['DB_DSN'];
        $user = $_ENV['DB_USER'] ?? null;
        $pass = $_ENV['DB_PASS'] ?? null;
        try {
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            error_log('[db_connect] DSN connection failed: ' . $e->getMessage());
        }
    }

    // Fallback: use SQLite file in backend/data
    $sqlite = __DIR__ . '/data/app.db';
    try {
        if (!is_dir(dirname($sqlite))) {
            @mkdir(dirname($sqlite), 0755, true);
        }
        $pdo = new PDO('sqlite:' . $sqlite);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ensure minimal tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT UNIQUE,
            password TEXT,
            created_at TEXT
        );");
        $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            phone TEXT,
            subject TEXT,
            message TEXT,
            ip TEXT,
            user_agent TEXT,
            created_at TEXT
        );");
        $pdo->exec("CREATE TABLE IF NOT EXISTS admissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            contact_name TEXT,
            contact_phone TEXT,
            contact_email TEXT,
            resident_name TEXT,
            timeline TEXT,
            room_type TEXT,
            additional_info TEXT,
            ip TEXT,
            user_agent TEXT,
            created_at TEXT
        );");

        return $pdo;
    } catch (PDOException $e) {
        error_log('[db_connect] SQLite connection failed: ' . $e->getMessage());
        return null;
    }
}
?>