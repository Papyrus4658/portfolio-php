<?php
declare(strict_types=1);
require __DIR__ . '/dbconnect.php';
session_start();

$page_title = 'ログアウト';
$css_root = 'css';

// POST でログアウト実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    session_unset();
    session_destroy();
}
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="site-main no-sidebar">
    <div class="content login-wrap">
        <h1 class="page-title">ログアウト</h1>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="done-box">
                <p>ログアウトしました。</p>
                <a href="./" class="btn">トップへ</a>
                <a href="./login.php" class="btn btn-primary">再ログイン</a>
            </div>
        <?php else: ?>
            <div class="form-card">
                <p style="margin-bottom:1rem;">本当にログアウトしますか？</p>
                <form action="/logout.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">ログアウト</button>
                        <a href="./admin/" class="btn">キャンセル</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>