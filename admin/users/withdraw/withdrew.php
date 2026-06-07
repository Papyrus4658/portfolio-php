<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/admin/users/withdraw/');
csrf_verify();

// 論理削除
$st = $db->prepare('UPDATE users SET delete_flag = 1, updated_at = NOW() WHERE id = ?');
$st->execute([$user['id']]);

// セッション破棄
session_unset();
session_destroy();

$page_title = '退会完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main no-sidebar">
    <div class="content login-wrap">
        <h1 class="page-title">退会完了</h1>
        <div class="done-box">
            <p>退会手続きが完了しました。ご利用ありがとうございました。</p>
            <a href="/" class="btn btn-primary">トップへ</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>