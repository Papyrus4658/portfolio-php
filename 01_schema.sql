-- ============================================================
-- Portfolio DB Schema
-- ============================================================
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portfolio_db;
-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id BIGINT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    -- BCrypt ハッシュ
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    delete_flag TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE INDEX uq_users_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ------------------------------------------------------------
-- projects（作品）
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS projects (
    id BIGINT NOT NULL AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    thumbnail_url VARCHAR(500),
    repository_url VARCHAR(500),
    demo_url VARCHAR(500),
    -- デモサイトURL（任意）
    tech_stack VARCHAR(255),
    -- 例: "Java, Spring Boot, MySQL"
    sort_order INT NOT NULL DEFAULT 0,
    -- ギャラリー表示順（昇順）
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    delete_flag TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_projects_sort (sort_order, delete_flag)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ------------------------------------------------------------
-- articles（ブログ記事）
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS articles (
    id BIGINT NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT,
    -- Markdown 形式を想定
    thumbnail_url VARCHAR(500),
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    published_at DATETIME,
    -- 公開日時（status='published' 時に設定）
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    delete_flag TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_articles_status_published (
        status,
        published_at,
        delete_flag
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;