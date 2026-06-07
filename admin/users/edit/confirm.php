<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('index.php');
csrf_verify();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

function redirect_err_edit(string $msg): never
{
    flash_set('error', $msg);
    header('Location: index.php');
    exit;
}

if ($username === '')
    redirect_err_edit('ユーザー名は必須です。');
if (mb_strlen($username) > 50)
    redirect_err_edit('ユーザー名は50文字以内で入力してください。');
if ($email === '')
    redirect_err_edit('メールアドレスは必須です。');

// パスワード変更する場合のみ検証
$change_password = ($password !== '');
if ($change_password) {
    if (mb_strlen($password) < 8)
        redirect_err_edit('パスワードは8文字以上で入力してください。');
    if ($password !== $password_confirm)
        redirect_err_edit('パスワードが一致しません。');
}

// ユーザー名重複（自分以外）
$st = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND id != ?');
$st->execute([$username, $user['id']]);
if ((int) $st->fetchColumn() > 0)
    redirect_err_edit('既に使用されているユーザー名です。');

// メール重複（自分以外）
$st = $db->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ?');
$st->execute([$email, $user['id']]);
if ((int) $st->fetchColumn() > 0)
    redirect_err_edit('既に使用されているメールアドレスです。');

$hash = $change_password ? password_hash($password, PASSWORD_BCRYPT) : null;
$pw_mask = $change_password ? str_repeat('*', mb_strlen($password)) : '（変更なし）';

$_SESSION['user_edit'] = [
    'username' => $username,
    'email' => $email,
    'hash' => $hash,
    'pw_mask' => $pw_mask,
    'change_password' => $change_password,
];

$page_title = '編集内容確認';
$css_root = '../../../css';
$d = $_SESSION['user_edit'];
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">編集内容確認</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">以下の内容で更新します。よろしいですか？</p>

            <table class="confirm-table">
                <tr>
                    <th>ユーザー名</th>
                    <td><?= h($d['username']) ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($d['email']) ?></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td><?= h($d['pw_mask']) ?></td>
                </tr>
            </table>

            <form action="edited.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="index.php" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>