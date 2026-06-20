<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/');
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    flash_set('error', '無効なIDです。');
    header('Location: /');
    exit;
}

$st = $db->prepare('SELECT title FROM works WHERE id = ? AND visible = 1');
$st->execute([$id]);
$work = $st->fetch();
if (!$work) {
    flash_set('error', '作品が見つかりませんでした。');
    header('Location: /');
    exit;
}

$st = $db->prepare('UPDATE works SET visible = 0, updated_at = NOW() WHERE id = ?');
$st->execute([$id]);

$page_title = '非表示完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">非表示完了</h1>
        <div class="done-box">
            <p>「<?= h($work['title']) ?>」を非表示にしました。</p>
            <div class="form-actions" style="justify-content:center;">
                <a href="/portfoli-php/admin/works/redisplay/" class="btn">非表示作品一覧</a>
                <a href="/portfolio-php/" class="btn btn-primary">作品一覧へ</a>
            </div>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>