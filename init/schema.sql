-- ============================================================
-- Portfolio DB Schema  (新版)
-- ============================================================
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;
CREATE USER 'portfolio_user' @'localhost' IDENTIFIED BY 'portfolio_pass';
GRANT ALL PRIVILEGES ON portfolio_db.* TO 'portfolio_user' @'localhost';
FLUSH PRIVILEGES;
-- ------------------------------------------------------------
-- users（管理者）
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id BIGINT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    -- BCrypt ハッシュ
    delete_flag TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE INDEX uq_users_email (email),
    UNIQUE INDEX uq_users_username (username)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ------------------------------------------------------------
-- works（作品）
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS works (
    id BIGINT NOT NULL AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    thumbnail VARCHAR(500) NOT NULL DEFAULT '',
    -- 相対パス (images/works/xxx.jpg)
    repo_url VARCHAR(500) NOT NULL DEFAULT '',
    description TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    -- 表示順（昇順）
    visible TINYINT(1) NOT NULL DEFAULT 1,
    -- 0=非表示, 1=公開
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_works_visible_sort (visible, sort_order)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;