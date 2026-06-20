<?php
declare(strict_types=1);
require __DIR__ . '/dbconnect.php';
session_start();

$page_title = 'ポートフォリオ';
$css_root = 'css';

// ログイン済みか
$logged_in = !empty($_SESSION['admin_id']);
$user = null;
if ($logged_in) {
    $st = $db->prepare('SELECT * FROM users WHERE id = ? AND delete_flag = 0');
    $st->execute([$_SESSION['admin_id']]);
    $user = $st->fetch();
    if (!$user) {
        unset($_SESSION['admin_id']);
        $logged_in = false;
    }
}

// 公開中の作品を取得
$st = $db->query('SELECT * FROM works WHERE visible = 1 ORDER BY id DESC');
$works = $st->fetchAll();
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<?php if ($logged_in): ?>
    <div class="site-main">
        <div class="content">
            <h1 class="page-title">作品一覧</h1>

            <?php if (empty($works)): ?>
                <p>登録されている作品はありません。</p>
            <?php else: ?>
                <div class="works-list">
                    <?php foreach ($works as $w): ?>
                        <div class="work-card">
                            <a class="work-figure" href="<?= url('/admin/works/edit/') ?>?id=<?= h($w['id']) ?>">
                                <img src="<?= h(url('/' . $w['thumbnail'])) ?>" alt="<?= h($w['title']) ?>のサムネイル">
                                <div class="work-figcaption">
                                    <h2><?= h($w['title']) ?></h2>
                                    <p class="work-outline"><?= nl2br(h($w['description'])) ?></p>
                                </div>
                            </a>
                            <div class="work-meta">
                                <span class="work-url">
                                    <a href="<?= h($w['repo_url']) ?>" target="_blank" rel="noopener">
                                        <?= h($w['repo_url']) ?>
                                    </a>
                                </span>
                                <span>登録: <?= h(date('Y/m/d', strtotime($w['created_at']))) ?></span>
                                <span>更新: <?= h(date('Y/m/d', strtotime($w['updated_at']))) ?></span>
                                <span class="work-actions">
                                    <a href="<?= url('/admin/works/edit/') ?>?id=<?= h($w['id']) ?>">[編集]</a>
                                    <a href="<?= url('/admin/works/hide/') ?>?id=<?= h($w['id']) ?>">[非表示]</a>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php $root = '/';
        require __DIR__ . '/layout/admin_sidebar.php'; ?>
    </div>

<?php else: ?>

    <div class="site-main no-sidebar">
        <div class="content works-list">
            <?php if (empty($works)): ?>
                <p>現在、作品はありません。</p>
            <?php else: ?>
                <?php foreach ($works as $w): ?>
                    <div class="work-card">
                        <div class="work-figure" style="text-decoration:none;color:inherit;">
                            <img src="<?= h(url('/' . $w['thumbnail'])) ?>" alt="<?= h($w['title']) ?>のサムネイル">
                            <div class="work-figcaption">
                                <h2><?= h($w['title']) ?></h2>
                                <p class="work-outline"><?= nl2br(h($w['description'])) ?></p>
                            </div>
                        </div>
                        <div class="work-meta">
                            <span class="work-url">
                                <a href="<?= h($w['repo_url']) ?>" target="_blank" rel="noopener">
                                    <?= h($w['repo_url']) ?>
                                </a>
                            </span>
                            <span>登録: <?= h(date('Y/m/d', strtotime($w['created_at']))) ?></span>
                            <span>更新: <?= h(date('Y/m/d', strtotime($w['updated_at']))) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align:right; margin-top:.5rem;">
            <a href="<?= url('/login.php') ?>" class="btn">管理者ログイン</a>
        </div>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>