-- database/schema.sql
-- Creates the DB + tables for the DATA Labo project.

CREATE DATABASE IF NOT EXISTS database_labo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE database_labo;

-- USERS
CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(254) NOT NULL,
    email VARCHAR(254) NULL,
    phone VARCHAR(20) NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id),
    UNIQUE KEY uq_users_user_name (user_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- POSTS
CREATE TABLE IF NOT EXISTS posts (
    post_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    post_title VARCHAR(254) NOT NULL,
    post_body MEDIUMTEXT NOT NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (post_id),
    KEY idx_posts_user_id (user_id),
    KEY idx_posts_status_created_at (status, created_at),

    CONSTRAINT fk_posts_users
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- COMMENTS
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    author_name VARCHAR(80) NOT NULL,
    author_email VARCHAR(254) NULL,
    comment_body TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (comment_id),
    KEY idx_comments_post_id_created_at (post_id, created_at),

    CONSTRAINT fk_comments_posts
        FOREIGN KEY (post_id) REFERENCES posts(post_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;