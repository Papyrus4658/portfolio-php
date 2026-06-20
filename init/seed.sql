-- ============================================================
-- 開発用サンプルデータ（ローカル環境のみ）
-- ============================================================
USE portfolio_db;

-- パスワード: "password123" の BCrypt ハッシュ
INSERT INTO
    users (
        username,
        email,
        password_hash
    )
VALUES (
        'admin',
        'admin@example.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );

-- サンプル作品
INSERT INTO
    works (
        title,
        thumbnail,
        repo_url,
        description,
        sort_order,
        visible
    )
VALUES (
        'ポートフォリオサイト（旧版）',
        'images/works/sample1.png',
        'https://github.com/yourname/portfolio-php',
        'PHP + MySQL で構築した個人ポートフォリオサイトです。',
        1,
        1
    ),
    (
        'TODOアプリ',
        'images/works/sample2.png',
        'https://github.com/yourname/todo-app',
        'シンプルなCRUDを実装したTODO管理アプリです。',
        2,
        1
    );