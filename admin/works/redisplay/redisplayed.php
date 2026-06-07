<?php
declare(strict_types=1);
require __DIR__ . '/../../../dbconnect.php';
session_start();

$user = require_login('/login.php');
require_post('/admin/works/redisplay/');
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    flash_set('error', '無効なIDです。');
    header('Location: index.php');
    exit;
}

$st = $db->prepare('SELECT title FROM works WHERE id = ? AND visible = 0');
$st->execute([$id]);
$work = $st->fetch();
if (!$work) {
    flash_set('error', '作品が見つかりませんでした。');
    header('Location: index.php');
    exit;
}

$st = $db->prepare('UPDATE works SET visible = 1, updated_at = NOW() WHERE id = ?');
$st->execute([$id]);

flash_set('success', '「' . $work['title'] . '」を再表示しました。');
header('Location: index.php');
exit;
