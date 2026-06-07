<?php
declare(strict_types=1);
require __DIR__ . '/dbconnect.php';
session_start();

// 既にログイン済みなら管理TOPへ
if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}

$page_title = '管理者ログイン';
$css_root = 'css';
$error = flash_get('error');
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="site-main no-sidebar">
    <div class="content login-wrap">
        <h1 class="page-title">管理者ログイン</h1>

        <?php if ($error): ?>
            <p class="flash-error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="form-card">
            <form action="/login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="email">メールアドレス</label></th>
                        <td><input type="email" name="email" id="email" required autofocus
                                placeholder="admin@example.com"></td>
                    </tr>
                    <tr>
                        <th><label for="password">パスワード</label></th>
                        <td><input type="password" name="password" id="password" required placeholder="8文字以上"></td>
                    </tr>
                </table>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">ログイン</button>
                    <a href="/">← 公開サイトへ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>