<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/admin/users/edit/');
csrf_verify();

$d = $_SESSION['user_edit'] ?? null;
if (!$d) {
    flash_set('error', '編集データが見つかりません。再度お試しください。');
    header('Location: index.php');
    exit;
}
unset($_SESSION['user_edit']);

if ($d['change_password']) {
    $st = $db->prepare(
        'UPDATE users SET username=?, email=?, password_hash=?, updated_at=NOW() WHERE id=?'
    );
    $st->execute([$d['username'], $d['email'], $d['hash'], $user['id']]);
} else {
    $st = $db->prepare(
        'UPDATE users SET username=?, email=?, updated_at=NOW() WHERE id=?'
    );
    $st->execute([$d['username'], $d['email'], $user['id']]);
}

$page_title = '編集完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">編集完了</h1>
        <div class="done-box">
            <p>アカウント情報を更新しました。</p>
            <div class="form-actions" style="justify-content:center;">
                <a href="/admin/" class="btn btn-primary">管理TOPへ</a>
            </div>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>