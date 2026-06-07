<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/admin/works/add/');
csrf_verify();

$d = $_SESSION['work_add'] ?? null;
if (!$d) {
    flash_set('error', '登録データが見つかりません。再度お試しください。');
    header('Location: index.php');
    exit;
}
unset($_SESSION['work_add']);

// 一時ファイルを images/works/ へ移動
$temp_path = __DIR__ . '/temp/' . $d['temp_file'];
$dest_dir = __DIR__ . '/../../../images/works/';
if (!is_dir($dest_dir))
    mkdir($dest_dir, 0755, true);

$dest_path = $dest_dir . $d['temp_file'];
if (!rename($temp_path, $dest_path)) {
    flash_set('error', 'ファイルの移動に失敗しました。');
    header('Location: index.php');
    exit;
}
delete_dir_contents(__DIR__ . '/temp/');

$thumbnail = 'images/works/' . $d['temp_file'];

$st = $db->prepare(
    'INSERT INTO works (title, thumbnail, repo_url, description, sort_order, visible, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())'
);
$st->execute([$d['title'], $thumbnail, $d['repo_url'], $d['description'], $d['sort_order']]);

$page_title = '登録完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">登録完了</h1>
        <div class="done-box">
            <p>「<?= h($d['title']) ?>」を登録しました。</p>
            <div class="form-actions" style="justify-content:center;">
                <a href="/admin/works/add/" class="btn btn-primary">続けて登録</a>
                <a href="/" class="btn">作品一覧へ</a>
            </div>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>