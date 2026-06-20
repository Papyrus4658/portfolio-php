<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/');
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$repo_url = trim($_POST['repo_url'] ?? '');
$description = trim($_POST['description'] ?? '');
$sort_order = max(0, (int) ($_POST['sort_order'] ?? 0));

function redirect_err(string $msg, int $id): never
{
    flash_set('error', $msg);
    header("Location: index.php?id={$id}");
    exit;
}

if (!$id)
    redirect_err('無効なIDです。', $id);
if ($title === '')
    redirect_err('作品名は必須です。', $id);
if (mb_strlen($title) > 100)
    redirect_err('作品名は100文字以内で入力してください。', $id);
if ($repo_url === '')
    redirect_err('リポジトリURLは必須です。', $id);
if ($description === '')
    redirect_err('概要は必須です。', $id);
if (mb_strlen($description) > 500)
    redirect_err('概要は500文字以内で入力してください。', $id);

// 作品存在確認
$st = $db->prepare('SELECT * FROM works WHERE id = ? AND visible = 1');
$st->execute([$id]);
$work = $st->fetch();
if (!$work)
    redirect_err('作品が見つかりません。', $id);

// 作品名重複（自分以外）
$st = $db->prepare('SELECT COUNT(*) FROM works WHERE title = ? AND id != ?');
$st->execute([$title, $id]);
if ((int) $st->fetchColumn() > 0)
    redirect_err('既に使用されている作品名です。', $id);

// URL重複（自分以外）
$st = $db->prepare('SELECT COUNT(*) FROM works WHERE repo_url = ? AND id != ?');
$st->execute([$repo_url, $id]);
if ((int) $st->fetchColumn() > 0)
    redirect_err('既に使用されているURLです。', $id);

// ファイルアップロード（任意）
$temp_filename = null;
$has_new_file = false;

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['file']['tmp_name']);

    if (!in_array($mime, $allowed_types, true))
        redirect_err('jpg/jpeg/png/gif のみアップロード可能です。', $id);
    if ($_FILES['file']['size'] > 2 * 1024 * 1024)
        redirect_err('ファイルサイズは2MB以内にしてください。', $id);

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    };
    $temp_dir = __DIR__ . '/temp/';
    if (!is_dir($temp_dir))
        mkdir($temp_dir, 0755, true);

    $temp_filename = bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($_FILES['file']['tmp_name'], $temp_dir . $temp_filename);
    $has_new_file = true;
}

$_SESSION['work_edit'] = [
    'id' => $id,
    'title' => $title,
    'repo_url' => $repo_url,
    'description' => $description,
    'sort_order' => $sort_order,
    'temp_file' => $temp_filename,
    'old_thumb' => $work['thumbnail'],
];

$page_title = '編集内容確認';
$css_root = '../../../css';
$d = $_SESSION['work_edit'];
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">編集内容確認</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">以下の内容で更新します。よろしいですか？</p>

            <table class="confirm-table">
                <tr>
                    <th>作品名</th>
                    <td><?= h($d['title']) ?></td>
                </tr>
                <tr>
                    <th>サムネイル</th>
                    <td>
                        <?php if ($d['temp_file']): ?>
                            <img class="preview-img" src="temp/<?= h($d['temp_file']) ?>" alt="新しいサムネイル">
                            <small class="mt-1" style="display:block;">（新しい画像に変更）</small>
                        <?php else: ?>
                            <img class="preview-img" src="/<?= h($d['old_thumb']) ?>" alt="現在のサムネイル">
                            <small class="mt-1" style="display:block;">（変更なし）</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>リポジトリURL</th>
                    <td><?= h($d['repo_url']) ?></td>
                </tr>
                <tr>
                    <th>概要</th>
                    <td><?= nl2br(h($d['description'])) ?></td>
                </tr>
                <tr>
                    <th>表示順</th>
                    <td><?= h((string) $d['sort_order']) ?></td>
                </tr>
            </table>

            <form action="edited.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="./index.php?id=<?= h((string) $d['id']) ?>" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>