<?php
declare(strict_types=1);
require __DIR__ . '/dbconnect.php';
session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: /portfolio-php/admin/');
    exit;
}

require_post('/portfolio-php/login.php');
csrf_verify();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$st = $db->prepare('SELECT * FROM users WHERE email = ? AND delete_flag = 0');
$st->execute([$email]);
$user = $st->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    // セッション固定攻撃対策
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    header('Location: /admin/');
    exit;
}

flash_set('error', 'メールアドレスまたはパスワードが違います。');
header('Location: /portfolio-php/login.php');
exit;
