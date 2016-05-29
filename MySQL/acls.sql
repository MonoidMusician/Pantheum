CREATE DATABASE IF NOT EXISTS latin
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_unicode_ci;

USE latin;

DROP TABLE IF EXISTS class_grades;
DROP TABLE IF EXISTS class_quiz;
DROP TABLE IF EXISTS class_acls;
DROP TABLE IF EXISTS class;
DROP TABLE IF EXISTS acls;

CREATE TABLE acls (
    user_id BIGINT UNSIGNED NOT NULL,
    admin_panel CHAR(2),
    add_words CHAR(2),
    teacher_panel CHAR(2),
    class_settings CHAR(2),
    user_settings CHAR(2),
    user_password CHAR(2),

        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE class (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100),
    description VARCHAR(1024)
);

CREATE TABLE class_acls (
    user_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    grades CHAR(2),
    create_quiz CHAR(2),
    add_users CHAR(2),
    take_quiz CHAR(2),

        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (class_id) REFERENCES class(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE class_quiz (
    id BIGINT UNSIGNED PRIMARY KEY,
    class_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    name VARCHAR(100),
    description VARCHAR(1024),
    VISIBLE BOOLEAN,
    total_points INT,
    qdata text,

        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (class_id) REFERENCES class(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE class_grades (
    user_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    class_quiz_id BIGINT UNSIGNED NOT NULL,
    automatic_grade_earned INT,
    automatic_grade_total INT,
    grade_earned INT,
    grade_total INT,

        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (class_id) REFERENCES class(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (class_quiz_id) REFERENCES class_quiz(id) ON DELETE CASCADE ON UPDATE CASCADE
);
