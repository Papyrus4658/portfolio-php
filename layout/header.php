<?php
/**
 * layout/header.php
 * 使い方: require __DIR__ . '/../layout/header.php';
 * 呼び出し前に $page_title を定義すること。
 * $css_root: CSSルートへの相対パス（省略時 'css'）
 */
$css_root = $css_root ?? 'css';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'ポートフォリオ') ?></title>
    <link rel="stylesheet" href="<?= $css_root ?>/style.css">
    <link rel="icon" href="<?= url('/images/favicon.png') ?>" type="image/png">
</head>

<body>
    <header class="site-header">
        <a href="<?= url('/') ?>" class="site-title">Portfolio</a>
    </header>
    <main class="site-main">