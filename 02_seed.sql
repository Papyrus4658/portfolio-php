-- ============================================================
-- 開発用サンプルデータ（ローカル環境のみ）
-- ============================================================
USE portfolio_db;
-- users: パスワードは "password" の BCrypt ハッシュ（Spring Security で照合）
INSERT INTO
    users (
        username,
        email,
        password_hash
    )
VALUES (
        'admin',
        'admin@example.com',
        '$2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'
    );
-- projects
INSERT INTO
    projects (
        title,
        description,
        thumbnail_url,
        repository_url,
        demo_url,
        tech_stack,
        sort_order
    )
VALUES (
        'ポートフォリオサイト',
        'Spring Boot + Thymeleaf + MySQL で構築した個人サイトです。',
        '/images/thumb_portfolio.png',
        'https://codeberg.org/yourname/portfolio',
        NULL,
        'Java, Spring Boot, Thymeleaf, MySQL, Bootstrap',
        1
    ),
    (
        'サンプルTODOアプリ',
        'PHP で作成した初期作品。CRUD の基本を実装しています。',
        '/images/thumb_todo.png',
        'https://codeberg.org/yourname/todo-php',
        NULL,
        'PHP, MySQL, HTML, CSS',
        2
    );
-- articles
INSERT INTO
    articles (
        title,
        content,
        status,
        published_at
    )
VALUES (
        'Spring Boot 入門: Hello World から始める',
        '## はじめに\nこの記事では Spring Boot プロジェクトのセットアップ方法を解説します。\n\n## 手順\n1. Spring Initializr でプロジェクトを生成\n2. ...',
        'published',
        NOW()
    ),
    (
        'PHP から Java への移行で感じたこと（下書き）',
        '## PHP との比較\n型安全性の違いが最初は戸惑いましたが...',
        'draft',
        NULL
    );