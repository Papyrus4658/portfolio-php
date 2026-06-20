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

$page_title = '作品非表示';
$css_root = '../../../css';
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">作品非表示</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">以下の作品を非表示にします。よろしいですか？</p>

            <table class="confirm-table">
                <tr>
                    <th>作品名</th>
                    <td><?= h($work['title']) ?></td>
                </tr>
                <tr>
                    <th>サムネイル</th>
                    <td><img class="preview-img" src="/<?= h($work['thumbnail']) ?>" alt="サムネイル"></td>
                </tr>
                <tr>
                    <th>概要</th>
                    <td><?= nl2br(h($work['description'])) ?></td>
                </tr>
                <tr>
                    <th>登録日時</th>
                    <td><?= h(date('Y/m/d H:i', strtotime($work['created_at']))) ?></td>
                </tr>
                <tr>
                    <th>最終更新</th>
                    <td><?= h(date('Y/m/d H:i', strtotime($work['updated_at']))) ?></td>
                </tr>
                <tr>
                    <th>リポジトリURL</th>
                    <td><?= h($work['repo_url']) ?></td>
                </tr>
            </table>

            <form action="hid.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= h((string) $work['id']) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">非表示にする</button>
                    <a href="./" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>