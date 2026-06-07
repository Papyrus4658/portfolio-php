<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
$page_title = 'アカウント編集';
$css_root = '../../../css';
$error = flash_get('error');
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">アカウント編集</h1>

        <?php if ($error): ?>
            <p class="flash-error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="form-card">
            <form action="confirm.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="username">ユーザー名 <span class="text-danger">*</span></label></th>
                        <td><input type="text" name="username" id="username" required maxlength="50"
                                value="<?= h($user['username']) ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="email">メールアドレス <span class="text-danger">*</span></label></th>
                        <td><input type="email" name="email" id="email" required value="<?= h($user['email']) ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="password">新しいパスワード</label></th>
                        <td><input type="password" name="password" id="password" placeholder="変更する場合のみ入力（8文字以上）"
                                minlength="8"></td>
                    </tr>
                    <tr>
                        <th><label for="password_confirm">パスワード確認</label></th>
                        <td><input type="password" name="password_confirm" id="password_confirm" placeholder="上と同じパスワード"
                                minlength="8"></td>
                    </tr>
                </table>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">確認</button>
                    <a href="/admin/" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>