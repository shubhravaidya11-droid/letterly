CREATE DATABASE IF NOT EXISTS letterly_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE letterly_db;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    username VARCHAR(40) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS letters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(160) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    template VARCHAR(32) NOT NULL DEFAULT 'rose',
    font_family VARCHAR(24) NOT NULL DEFAULT 'serif',
    text_size TINYINT UNSIGNED NOT NULL DEFAULT 18,
    text_color CHAR(7) NOT NULL DEFAULT '#49372f',
    text_align VARCHAR(12) NOT NULL DEFAULT 'left',
    paper_color CHAR(7) NOT NULL DEFAULT '#fffdf8',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_letters_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_letters_user_updated (user_id, updated_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS letter_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    letter_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_letter FOREIGN KEY (letter_id) REFERENCES letters(id) ON DELETE CASCADE,
    INDEX idx_images_letter (letter_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS letter_stickers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    letter_id BIGINT UNSIGNED NOT NULL,
    sticker_path VARCHAR(32) NOT NULL,
    position_x DECIMAL(6,2) NOT NULL DEFAULT 80,
    position_y DECIMAL(6,2) NOT NULL DEFAULT 12,
    width DECIMAL(6,2) NOT NULL DEFAULT 48,
    height DECIMAL(6,2) NOT NULL DEFAULT 48,
    CONSTRAINT fk_stickers_letter FOREIGN KEY (letter_id) REFERENCES letters(id) ON DELETE CASCADE,
    INDEX idx_stickers_letter (letter_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS remember_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    selector CHAR(32) NOT NULL UNIQUE,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    CONSTRAINT fk_remember_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_remember_expiration (expires_at)
) ENGINE=InnoDB;