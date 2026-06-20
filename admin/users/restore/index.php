<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

// ログイン済みなら管理TOPへ
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . url('/admin/'));
    exit;
}

$page_title = 'アカウント復元';
$css_root = '../../../css';
$error = flash_get('error');
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main no-sidebar">
    <div class="content login-wrap">
        <h1 class="page-title">アカウント復元</h1>

        <?php if ($error): ?>
            <p class="flash-error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="form-card">
            <p style="margin-bottom:1rem;">
                退会済みのアカウントを復元します。<br>
                登録時のメールアドレスとパスワードを入力してください。
            </p>
            <form action="restored.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="email">メールアドレス</label></th>
                        <td><input type="email" name="email" id="email" required></td>
                    </tr>
                    <tr>
                        <th><label for="password">パスワード</label></th>
                        <td><input type="password" name="password" id="password" required></td>
                    </tr>
                </table>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">復元</button>
                    <a href="<?= url('/login.php') ?>" class="btn">ログインへ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>