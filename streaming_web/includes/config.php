<?php
// ============================================================
// CONFIGURACIÓN DE BASE DE DATOS
// XAMPP local: host=localhost, user=root, pass='', db=streaming_db
// Hostinger: cambiar los valores por los de tu panel de hosting
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Sin contraseña en XAMPP
define('DB_NAME', 'streaming_db');
define('DB_PORT', 3306);

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');

// URL base de la app (cambia al subir a Hostinger)
define('APP_URL', 'http://localhost/streaming_web');
define('API_URL', APP_URL . '/api');

// Tiempo de expiración del token API (en horas)
define('TOKEN_EXPIRE_HOURS', 24);

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

session_start();

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . APP_URL . '/index.php');
        exit;
    }
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
