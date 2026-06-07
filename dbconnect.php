<?php
declare(strict_types=1);

// --- 設定 ---
define('DB_DSN', 'mysql:dbname=portfolio_db;host=127.0.0.1;charset=utf8mb4');
define('DB_USER', 'portfolio_user');
define('DB_PASS', 'portfolio_pass');

// --- PDO接続 ---
try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('DB接続エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
}

// --- ユーティリティ ---

/** HTMLエスケープ */
function h(mixed $str): string
{
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

/** CSRFトークン生成（セッションに保存） */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** CSRFトークン検証（失敗時は403で終了） */
function csrf_verify(): void
{
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('不正なリクエストです。');
    }
    // 使い捨て
    unset($_SESSION['csrf_token']);
}

/** セッションから1回だけ取得するフラッシュメッセージ */
function flash_get(string $key): string
{
    $msg = $_SESSION[$key] ?? '';
    unset($_SESSION[$key]);
    return (string) $msg;
}

/** セッションにフラッシュメッセージをセット */
function flash_set(string $key, string $msg): void
{
    $_SESSION[$key] = $msg;
}

/** ログイン済みでなければリダイレクト */
function require_login(string $redirect = '/index.php'): array
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . $redirect);
        exit;
    }
    global $db;
    $st = $db->prepare('SELECT * FROM users WHERE id = ? AND delete_flag = 0');
    $st->execute([$_SESSION['admin_id']]);
    $user = $st->fetch();
    if (!$user) {
        unset($_SESSION['admin_id']);
        header('Location: ' . $redirect);
        exit;
    }
    return $user;
}

/** POST専用。GETなら$toへリダイレクト */
function require_post(string $to = '../index.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . $to);
        exit;
    }
}

/**
 * ディレクトリ内の全ファイルを削除（サブディレクトリ含む）
 */
function delete_dir_contents(string $dir): void
{
    if (!is_dir($dir))
        return;
    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    ) as $path) {
        $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
    }
}
