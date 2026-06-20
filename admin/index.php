<?php
declare(strict_types=1);
require __DIR__ . '/../dbconnect.php';
session_start();

$user = require_login('/login.php');
$page_title = '管理TOP';
$css_root = '../css';

$st = $db->query('SELECT COUNT(*) FROM works WHERE visible = 1');
$count_visible = (int) $st->fetchColumn();

$st = $db->query('SELECT COUNT(*) FROM works WHERE visible = 0');
$count_hidden = (int) $st->fetchColumn();
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">管理TOP</h1>

        <div class="form-card">
            <table class="confirm-table">
                <tr>
                    <th>公開中の作品数</th>
                    <td><?= $count_visible ?> 件</td>
                </tr>
                <tr>
                    <th>非表示中の作品数</th>
                    <td><?= $count_hidden ?> 件</td>
                </tr>
                <tr>
                    <th>ログイン中</th>
                    <td><?= h($user['username']) ?> さん</td>
                </tr>
            </table>

            <div class="form-actions">
                <a href="./admin/works/add/" class="btn btn-primary">作品登録</a>
                <a href="./" class="btn">公開サイトを確認</a>
            </div>
        </div>
    </div>

    <?php $root = '../';
    require __DIR__ . '/../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>