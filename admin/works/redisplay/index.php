<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');

$page_title = '非表示作品一覧';
$css_root = '../../../css';

$st = $db->query('SELECT * FROM works WHERE visible = 0 ORDER BY updated_at DESC');
$works = $st->fetchAll();

$success = flash_get('success');
$error = flash_get('error');
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">非表示作品一覧</h1>

        <?php if ($success): ?>
            <p class="flash-success"><?= h($success) ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="flash-error"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if (empty($works)): ?>
            <div class="form-card">
                <p>非表示中の作品はありません。</p>
            </div>
        <?php else: ?>
            <div class="form-card" style="overflow-x:auto;">
                <table class="hidden-works-table">
                    <thead>
                        <tr>
                            <th>サムネイル</th>
                            <th>作品名</th>
                            <th>概要</th>
                            <th>登録日時</th>
                            <th>最終更新</th>
                            <th>リポジトリURL</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($works as $w): ?>
                            <tr>
                                <td><img src="/<?= h($w['thumbnail']) ?>" alt="サムネイル"></td>
                                <td><?= h($w['title']) ?></td>
                                <td class="work-outline-cell"><?= h($w['description']) ?></td>
                                <td><?= h(date('Y/m/d H:i', strtotime($w['created_at']))) ?></td>
                                <td><?= h(date('Y/m/d H:i', strtotime($w['updated_at']))) ?></td>
                                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <a href="<?= h($w['repo_url']) ?>" target="_blank" rel="noopener">
                                        <?= h($w['repo_url']) ?>
                                    </a>
                                </td>
                                <td>
                                    <form action="redisplayed.php" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= h((string) $w['id']) ?>">
                                        <button type="submit" class="btn btn-primary"
                                            style="font-size:.8rem;padding:.3rem .8rem;">
                                            再表示
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>