<?php
/**
 * layout/admin_sidebar.php
 * $user     : ログイン中ユーザー配列
 * $root     : サイトルートへの相対パス（例 '../../'）
 */
$root = $root ?? '/';
?>
<aside class="sidebar">
    <p class="sidebar-user">👤 <?= h($user['username']) ?> さん</p>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="<?= $root ?>">公開サイト</a></li>
            <li><a href="./admin/">管理TOP</a></li>
            <li><a href="./admin/works/add/">作品登録</a></li>
            <li><a href="./admin/works/redisplay/">非表示作品</a></li>
            <li><a href="./admin/users/add/">管理者登録</a></li>
            <li><a href="./admin/users/edit/">アカウント編集</a></li>
            <li><a href="./logout.php">ログアウト</a></li>
            <li><a href="./admin/users/withdraw/">退会</a></li>
        </ul>
    </nav>
</aside>