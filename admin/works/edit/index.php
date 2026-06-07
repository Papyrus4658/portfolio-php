<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    flash_set('error', '無効な作品IDです。');
    header('Location: /');
    exit;
}

$st = $db->prepare('SELECT * FROM works WHERE id = ? AND visible = 1');
$st->execute([$id]);
$work = $st->fetch();
if (!$work) {
    flash_set('error', '作品が見つかりませんでした。');
    header('Location: /');
    exit;
}

$page_title = '作品編集';
$css_root = '../../../css';
$error = flash_get('error');
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">作品編集</h1>

        <?php if ($error): ?>
            <p class="flash-error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="form-card">
            <form action="confirm.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= h((string) $work['id']) ?>">

                <table class="form-table">
                    <tr>
                        <th><label for="title">作品名 <span class="text-danger">*</span></label></th>
                        <td><input type="text" name="title" id="title" required maxlength="100"
                                value="<?= h($work['title']) ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="file">サムネイル変更</label></th>
                        <td>
                            <img src="/<?= h($work['thumbnail']) ?>" alt="現在のサムネイル"
                                style="max-width:120px;margin-bottom:.5rem;">
                            <input type="file" name="file" id="file" accept=".jpg,.jpeg,.png,.gif">
                            <small>変更する場合のみ選択（jpg/jpeg/png/gif・2MB以内）</small>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="repo_url">リポジトリURL <span class="text-danger">*</span></label></th>
                        <td><input type="url" name="repo_url" id="repo_url" required
                                value="<?= h($work['repo_url']) ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="description">概要 <span class="text-danger">*</span></label></th>
                        <td><textarea name="description" id="description" required
                                maxlength="500"><?= h($work['description']) ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="sort_order">表示順</label></th>
                        <td><input type="number" name="sort_order" id="sort_order"
                                value="<?= h((string) $work['sort_order']) ?>" min="0" max="9999"></td>
                    </tr>
                </table>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">確認</button>
                    <a href="/" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>