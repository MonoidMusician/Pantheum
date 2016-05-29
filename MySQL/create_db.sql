# SERIAL = BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE

CREATE DATABASE IF NOT EXISTS latin
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_unicode_ci;

USE latin;

DROP TABLE IF EXISTS connections;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS pronunciations;
DROP TABLE IF EXISTS definitions;
DROP TABLE IF EXISTS forms;
DROP TABLE IF EXISTS words;
DROP TABLE IF EXISTS lang_aliases;
DROP TABLE IF EXISTS languages;
CREATE TABLE languages
(
    lang_id CHAR(6) NOT NULL PRIMARY KEY,
    lang_dispname VARCHAR(100)
);
CREATE TABLE lang_aliases
(
    lang_name VARCHAR(100) PRIMARY KEY,
    lang_id CHAR(6) NOT NULL,
        FOREIGN KEY (lang_id) REFERENCES languages(lang_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE words
(
    word_id SERIAL PRIMARY KEY,
    word_spart VARCHAR(30),
    word_name VARCHAR(200) NOT NULL,
    word_lang CHAR(6) NOT NULL,
        FOREIGN KEY (word_lang) REFERENCES languages(lang_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE forms
(
    form_id SERIAL PRIMARY KEY,
    word_id BIGINT UNSIGNED NOT NULL,
    form_tag VARCHAR(255),
    form_value VARCHAR(200),
    form_usage VARCHAR(255),
        FOREIGN KEY (word_id) REFERENCES words(word_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE definitions
(
    def_id SERIAL PRIMARY KEY,
    word_id BIGINT UNSIGNED NOT NULL,
    form_tag VARCHAR(255),
    def_sublang CHAR(6),
    def_sense VARCHAR(200),
    def_lang CHAR(6) NOT NULL,
    def_src VARCHAR(255),
    def_type ENUM('main', 'gloss',
                  'translation',
                  'usage'),
    def_value TEXT,
        FOREIGN KEY (def_sublang) REFERENCES languages(lang_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (def_lang) REFERENCES languages(lang_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (word_id) REFERENCES forms(word_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE pronunciations
(
    pron_id SERIAL PRIMARY KEY,
    word_id BIGINT UNSIGNED NOT NULL,
    form_tag VARCHAR(255),
    pron_sublang CHAR(6),
    pron_type CHAR(8),
    pron_src VARCHAR(255),
    pron_value VARCHAR(255),
    sound_file VARCHAR(200),
        FOREIGN KEY (pron_sublang) REFERENCES languages(lang_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (word_id) REFERENCES forms(word_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE attributes
(
    word_id BIGINT UNSIGNED NOT NULL,
    attr_tag VARCHAR(255),
    attr_value VARCHAR(255),
        FOREIGN KEY (word_id) REFERENCES words(word_id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE connections
(
    from_word_id BIGINT UNSIGNED NOT NULL,
    to_word_id BIGINT UNSIGNED NOT NULL,
    connect_type VARCHAR(60),
        FOREIGN KEY (from_word_id) REFERENCES words(word_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (to_word_id) REFERENCES words(word_id) ON DELETE CASCADE ON UPDATE CASCADE
);
