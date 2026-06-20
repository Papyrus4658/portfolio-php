<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/');
csrf_verify();

$d = $_SESSION['work_edit'] ?? null;
if (!$d) {
    flash_set('error', '編集データが見つかりません。再度お試しください。');
    header('Location: index.php');
    exit;
}
unset($_SESSION['work_edit']);

$thumbnail = $d['old_thumb'];

if ($d['temp_file']) {
    $temp_path = __DIR__ . '/temp/' . $d['temp_file'];
    $dest_dir = __DIR__ . '/../../../images/works/';
    if (!is_dir($dest_dir))
        mkdir($dest_dir, 0755, true);
    $dest_path = $dest_dir . $d['temp_file'];

    if (!rename($temp_path, $dest_path)) {
        flash_set('error', 'ファイルの移動に失敗しました。');
        header("Location: index.php?id={$d['id']}");
        exit;
    }
    delete_dir_contents(__DIR__ . '/temp/');

    // 旧画像を削除（images/works/ 以下のみ）
    $old = __DIR__ . '/../../../' . $d['old_thumb'];
    if (str_contains($d['old_thumb'], 'images/works/') && file_exists($old)) {
        unlink($old);
    }

    $thumbnail = 'images/works/' . $d['temp_file'];
}

$st = $db->prepare(
    'UPDATE works SET title=?, thumbnail=?, repo_url=?, description=?, sort_order=?, updated_at=NOW()
     WHERE id=?'
);
$st->execute([$d['title'], $thumbnail, $d['repo_url'], $d['description'], $d['sort_order'], $d['id']]);

$page_title = '編集完了';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">編集完了</h1>
        <div class="done-box">
            <p>「<?= h($d['title']) ?>」を更新しました。</p>
            <div class="form-actions" style="justify-content:center;">
                <a href="<?= url('/admin/works/edit/') ?>?id=<?= h((string) $d['id']) ?>" class="btn">続けて編集</a>
                <a href="<?= url('/') ?>" class="btn btn-primary">作品一覧へ</a>
            </div>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>