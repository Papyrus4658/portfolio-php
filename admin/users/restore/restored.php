<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}

require_post('index.php');
csrf_verify();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// delete_flag=1（退会済み）のユーザーを検索
$st = $db->prepare('SELECT * FROM users WHERE email = ? AND delete_flag = 1');
$st->execute([$email]);
$target = $st->fetch();

if (!$target || !password_verify($password, $target['password_hash'])) {
    flash_set('error', 'メールアドレスまたはパスワードが違います。退会済みアカウントが見つかりませんでした。');
    header('Location: index.php');
    exit;
}

$st = $db->prepare('UPDATE users SET delete_flag = 0, updated_at = NOW() WHERE id = ?');
$st->execute([$target['id']]);

$page_title = '復元完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main no-sidebar">
    <div class="content login-wrap">
        <h1 class="page-title">復元完了</h1>
        <div class="done-box">
            <p>「<?= h($target['username']) ?>」のアカウントを復元しました。</p>
            <a href="/login.php" class="btn btn-primary">ログイン</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>