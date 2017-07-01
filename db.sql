SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------
-- --------------- DROPS ----------------
-- --------------------------------------
DROP TABLE IF EXISTS administration_setting;
DROP TABLE IF EXISTS site_setting;
DROP TABLE IF EXISTS color;
DROP TABLE IF EXISTS date;
DROP TABLE IF EXISTS locale;
DROP TABLE IF EXISTS editor;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS file;
DROP TABLE IF EXISTS page;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS user;

-- --------------------------------------
-- -------------- CREATES ---------------
-- --------------------------------------
CREATE TABLE `administration_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color_id` int(11) NOT NULL,
  `locale_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `type` int(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `header_footer` int(11) NOT NULL,
  `showTitle` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `showTitle` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hexa` varchar(32) NOT NULL,
  `class` varchar(32) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `string` varchar(255) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `editor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `locale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `shortcut` varchar(255) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `page_order` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `site_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  `homepage` int(11) DEFAULT NULL,
  `registration` int(11) NOT NULL,
  `default_role` int(11) NOT NULL,
  `locale_id` int(11) NOT NULL,
  `not_found_page` int(11) DEFAULT NULL,
  `scripts` text NOT NULL,
  `articles_per_page` int(11) NOT NULL,
  `articles_preview_length` int(11) NOT NULL,
  `date_id` int(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0' COMMENT '0 - editor, 1 - admin',
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

-- --------------------------------------
-- -------------- INSERTS ---------------
-- --------------------------------------
INSERT INTO `administration_setting` (`id`, `color_id`, `locale_id`, `user_id`) VALUES
(1, 6, 1, 1);

INSERT INTO `article` (`id`, `title`, `content`, `date`, `description`, `keywords`, `type`, `url`, `user_id`, `category_id`, `header_footer`, `showTitle`, `published`) VALUES
(1, 'Under development', '<p style=\"text-align: center;\"><img class=\"responsive-img\" src=\"/Projects/Raiper-CMS/upload/development.jpg\" width=\"524\" height=\"524\" /></p>', '2017-07-01 12:56:00', 'This page was made in RA-CMS.', 'RA-CMS,', 0, 'under-development', 1, NULL, 1, 1, 1),
(2, 'Blog page', '<p style=\"text-align: center;\"><img class=\"responsive-img\" src=\"/Projects/Raiper-CMS/upload/OO13AS0.jpg\" width=\"403\" height=\"403\" /></p>', '2017-07-01 12:59:11', 'Blog page waqs made in RA-CMS.', 'RA-CMS,', 0, 'article66-2017-07-01', 1, 1, 1, 1, 1),
(3, '404', '<p style=\"text-align: center;\"><img class=\"responsive-img\" src=\"/Projects/Raiper-CMS/upload/404.jpg\" width=\"507\" height=\"507\" /></p>', '2017-07-01 15:09:00', '404 page not found', '404, page not found', 0, '404', 1, NULL, 1, 1, 1);

INSERT INTO `category` (`id`, `name`, `keywords`, `description`, `url`, `showTitle`) VALUES
(1, 'Blog', '', '', 'blog', 1);

INSERT INTO `color` (`id`, `hexa`, `class`) VALUES
(1, '#f44336', 'red'),
(2, '#e91e63', 'pink'),
(3, '#9c27b0', 'purple'),
(4, '#673ab7', 'deep-purple'),
(5, '#3f51b5', 'indigo'),
(6, '#2196f3', 'blue'),
(7, '#03a9f4', 'light-blue'),
(8, '#00bcd4', 'cyan'),
(9, '#009688', 'teal'),
(10, '#4caf50', 'green'),
(11, '#8bc34a', 'light-green'),
(12, '#cddc39', ' lime'),
(13, '#ffeb3b', 'yellow'),
(14, '#ffc107', 'amber'),
(15, '#ff9800', 'orange'),
(16, '#ff5722', 'deep-orange'),
(17, '#795548', 'brown'),
(18, '#9e9e9e', 'grey');

INSERT INTO `date` (`id`, `string`) VALUES
(1, '%d.%m.%Y %H:%M');

INSERT INTO `editor` (`id`, `name`) VALUES
(1, 'xcode'),
(2, 'cobalt'),
(3, 'chrome'),
(4, 'monokai'),
(5, 'terminal'),
(6, 'twilight');

INSERT INTO `file` (`id`, `name`, `extension`, `path`, `type`, `date`, `user_id`) VALUES
(1, '404', 'jpg', '/upload/404.jpg', 'image/jpeg', '2017-07-01 15:13:31', 1),
(2, 'development', 'jpg', '/upload/development.jpg', 'image/jpeg', '2017-07-01 15:13:31', 1);

INSERT INTO `locale` (`id`, `country`, `name`, `shortcut`) VALUES
(1, 'USA', 'English', 'en'),
(2, 'Slovakia', 'Slovak', 'sk');

INSERT INTO `page` (`id`, `name`, `article_id`, `category_id`, `page_order`) VALUES
(1, 'Home', 1, NULL, 1),
(2, 'Blog', NULL, 1, 2);

INSERT INTO `site_setting` (`id`, `site_name`, `color_id`, `homepage`, `registration`, `default_role`, `locale_id`, `not_found_page`, `scripts`, `articles_per_page`, `articles_preview_length`, `date_id`) VALUES
(1, 'RA-CMS website', 6, 1, 1, 1, 1, 3, '', 10, 100, 1);

INSERT INTO `user` (`id`, `login`, `password`, `mail`, `role`) VALUES
(1, 'Admin', '955db0b81ef1989b4a4dfeae8061a9a6', 'raipergm34@gmail.com', 1);

-- --------------------------------------
-- -------------- ALTERS ----------------
-- --------------------------------------
ALTER TABLE `administration_setting`
  ADD CONSTRAINT `administration_setting_color` FOREIGN KEY (`color_id`) REFERENCES `color` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `administration_setting_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `administration_setting_locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `article`
  ADD CONSTRAINT `article_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `article_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `comment`
  ADD CONSTRAINT `comment_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_article` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `file`
  ADD CONSTRAINT `file_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `page`
  ADD CONSTRAINT `page_article` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `page_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `site_setting`
  ADD CONSTRAINT `site_setting_article` FOREIGN KEY (`homepage`) REFERENCES `article` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `site_setting_locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `site_setting_article2` FOREIGN KEY (`not_found_page`) REFERENCES `article` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `site_setting_date` FOREIGN KEY (`date_id`) REFERENCES `date` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;