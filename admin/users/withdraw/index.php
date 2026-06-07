<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
$page_title = '退会';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">退会</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">
                以下のアカウントを退会（論理削除）します。<br>
                退会後はこのアカウントでログインできなくなります。本当によろしいですか？
            </p>

            <table class="confirm-table">
                <tr>
                    <th>ユーザー名</th>
                    <td><?= h($user['username']) ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($user['email']) ?></td>
                </tr>
            </table>

            <form action="withdrew.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">退会する</button>
                    <a href="/admin/" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>