<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/admin/users/add/');
csrf_verify();

$d = $_SESSION['user_add'] ?? null;
if (!$d) {
    flash_set('error', '登録データが見つかりません。再度お試しください。');
    header('Location: index.php');
    exit;
}
unset($_SESSION['user_add']);

$st = $db->prepare(
    'INSERT INTO users (username, email, password_hash, delete_flag, created_at, updated_at)
     VALUES (?, ?, ?, 0, NOW(), NOW())'
);
$st->execute([$d['username'], $d['email'], $d['hash']]);

$page_title = '登録完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">登録完了</h1>
        <div class="done-box">
            <p>「<?= h($d['username']) ?>」を管理者として登録しました。</p>
            <div class="form-actions" style="justify-content:center;">
                <a href="/admin/users/add/" class="btn">続けて登録</a>
                <a href="/admin/" class="btn btn-primary">管理TOPへ</a>
            </div>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>