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

function redirect_err_add(string $msg): never
{
    flash_set('error', $msg);
    header('Location: index.php');
    exit;
}

if ($username === '')
    redirect_err_add('ユーザー名は必須です。');
if (mb_strlen($username) > 50)
    redirect_err_add('ユーザー名は50文字以内で入力してください。');
if ($email === '')
    redirect_err_add('メールアドレスは必須です。');
if (mb_strlen($password) < 8)
    redirect_err_add('パスワードは8文字以上で入力してください。');
if ($password !== $password_confirm)
    redirect_err_add('パスワードが一致しません。');

$st = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$st->execute([$username]);
if ((int) $st->fetchColumn() > 0)
    redirect_err_add('既に使用されているユーザー名です。');

$st = $db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
$st->execute([$email]);
if ((int) $st->fetchColumn() > 0)
    redirect_err_add('既に使用されているメールアドレスです。');

$hash = password_hash($password, PASSWORD_BCRYPT);
$pw_mask = str_repeat('*', mb_strlen($password));

$_SESSION['user_add'] = [
    'username' => $username,
    'email' => $email,
    'hash' => $hash,
    'pw_mask' => $pw_mask,
];

$page_title = '登録内容確認';
$css_root = '../../../css';
$d = $_SESSION['user_add'];
?>
<?php require __DIR__ . '/../../../layout/header.php'; ?>

<div class="site-main">
    <div class="content">
        <h1 class="page-title">登録内容確認</h1>

        <div class="form-card">
            <p style="margin-bottom:1rem;">以下の内容で登録します。よろしいですか？</p>

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

            <form action="added.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">登録</button>
                    <a href="./index.php" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <?php $root = '../../../';
    require __DIR__ . '/../../../layout/admin_sidebar.php'; ?>
</div>

<?php require __DIR__ . '/../../../layout/footer.php'; ?>