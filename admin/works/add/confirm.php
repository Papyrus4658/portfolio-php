<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('index.php');
csrf_verify();

// --- 入力値取得 & バリデーション ---
$title = trim($_POST['title'] ?? '');
$repo_url = trim($_POST['repo_url'] ?? '');
$description = trim($_POST['description'] ?? '');
$sort_order = max(0, (int) ($_POST['sort_order'] ?? 0));

function redirect_with_error(string $msg): never
{
    flash_set('error', $msg);
    header('Location: index.php');
    exit;
}

if ($title === '')
    redirect_with_error('作品名は必須です。');
if (mb_strlen($title) > 100)
    redirect_with_error('作品名は100文字以内で入力してください。');
if ($repo_url === '')
    redirect_with_error('リポジトリURLは必須です。');
if ($description === '')
    redirect_with_error('概要は必須です。');
if (mb_strlen($description) > 500)
    redirect_with_error('概要は500文字以内で入力してください。');

// 作品名の重複チェック
$st = $db->prepare('SELECT COUNT(*) FROM works WHERE title = ?');
$st->execute([$title]);
if ((int) $st->fetchColumn() > 0)
    redirect_with_error('既に使用されている作品名です。');

// リポジトリURLの重複チェック
$st = $db->prepare('SELECT COUNT(*) FROM works WHERE repo_url = ?');
$st->execute([$repo_url]);
if ((int) $st->fetchColumn() > 0)
    redirect_with_error('既に使用されているURLです。');

// --- ファイルアップロード ---
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    redirect_with_error('ファイルのアップロードに失敗しました。');
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['file']['tmp_name']);

if (!in_array($mime, $allowed_types, true)) {
    redirect_with_error('アップロード可能なのは jpg / jpeg / png / gif のみです。');
}
if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
    redirect_with_error('ファイルサイズは2MB以内にしてください。');
}

$ext = match ($mime) {
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
};
$temp_dir = __DIR__ . '/temp/';
if (!is_dir($temp_dir))
    mkdir($temp_dir, 0755, true);

// 一時ファイル名：乱数を使い名前衝突を防ぐ
$temp_filename = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], $temp_dir . $temp_filename);

// 確認ページ用にセッションへ保存
$_SESSION['work_add'] = [
    'title' => $title,
    'repo_url' => $repo_url,
    'description' => $description,
    'sort_order' => $sort_order,
    'temp_file' => $temp_filename,
];

$page_title = '登録内容確認';
$css_root = '../../../css';
$d = $_SESSION['work_add'];
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">登録内容確認</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">以下の内容で登録します。よろしいですか？</p>

            <table class="confirm-table">
                <tr>
                    <th>作品名</th>
                    <td><?= h($d['title']) ?></td>
                </tr>
                <tr>
                    <th>サムネイル</th>
                    <td><img class="preview-img" src="temp/<?= h($d['temp_file']) ?>" alt="プレビュー"></td>
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

            <form action="added.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">登録</button>
                    <a href="index.php" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>