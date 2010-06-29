

UPDATE `modules` SET `dir_name` = '_core/imscp' WHERE `modules`.`dir_name` = '_core/content_packaging' LIMIT 1 ;

INSERT INTO `modules` VALUES ('_core/modules', 2, 0, 8192, 0, 0);

# --------------------------------------------------------
# Adding feature of oauth client
# Table structure for table `oauth_client_servers`
# since 1.6.5

CREATE TABLE `oauth_client_servers` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_server` VARCHAR(255) NOT NULL default '',
  `consumer_key` TEXT NOT NULL ,
  `consumer_secret` TEXT NOT NULL ,
  `expire_threshold` INT NOT NULL default 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id` ),
  UNIQUE INDEX idx_consumer ( `oauth_server` )
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `oauth_client_tokens`
# since 1.6.5

CREATE TABLE `oauth_client_tokens` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL,
  `token` VARCHAR(50) NOT NULL default '',
  `token_type` VARCHAR(50) NOT NULL NOT NULL default '',
  `token_secret` TEXT NOT NULL,
  `member_id` mediumint(8) unsigned NOT NULL ,
  `assign_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id`, `token` )
) TYPE=MyISAM;

# END Adding feature of oauth client

# -------------- Photo Album Module Setup ----------------
INSERT INTO `modules` VALUES ('_standard/photos',	 2, 16777216, 0, 0, 0);
# Photo Album Table
CREATE TABLE `pa_albums` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `permission` TINYINT(1) UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `photo_id` INTEGER UNSIGNED NOT NULL,
  `type_id` TINYINT(1) UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Photos Table
CREATE TABLE `pa_photos` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `alt_text` TEXT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `album_id` INTEGER UNSIGNED NOT NULL,
  `ordering` SMALLINT UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Course Album Table
CREATE TABLE `pa_course_album` (
  `course_id` INTEGER UNSIGNED,
  `album_id` INTEGER UNSIGNED,
  PRIMARY KEY (`course_id`, `album_id`)
)
ENGINE = MyISAM;

# Photo Album Comments
CREATE TABLE `pa_album_comments` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Photo Comments
CREATE TABLE `pa_photo_comments` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Initiali Config
INSERT INTO `config` VALUES ('pa_max_memory_per_member', '50');

# -------------- Photo Album Module Ends -----------------

# ----------------Flowplayer Module ------------------------
INSERT INTO `modules` VALUES ('_standard/flowplayer',	 2, 33554432, 0, 0, 0);

# Add Transformable configuration

INSERT INTO `config` (`name`, `value`) VALUES('transformable_uri', 'http://localhost/transformable/');
INSERT INTO `config` (`name`, `value`) VALUES('transformable_web_service_id', '90c3cd6f656739969847f3a99ac0f3c7');
INSERT INTO `config` (`name`, `value`) VALUES('transformable_oauth_expire', '93600');

# End of adding Transformable configuration

# Add the 1.6 series default theme as a secondary theme for ATutor 2.0
INSERT INTO `themes` VALUES ('ATutor 1.6', '2.0', 'default16', NOW(), 'This is the 1.6 series default theme.', 1);

# Add new field themes.type to seperate "Desktop" and "Mobile" themes
ALTER TABLE `themes` ADD `type` varchar(20) NOT NULL default 'Desktop' AFTER `dir_name`;

# point the module index pages to the new locations
UPDATE `courses` SET main_links=replace(main_links, 'glossary/index.php', 'mods/_core/glossary/index.php'), home_links=replace(home_links, 'glossary/index.php', 'mods/_core/glossary/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'file_storage/index.php', 'mods/_standard/file_storage/index.php'), home_links=replace(home_links, 'file_storage/index.php', 'mods/_standard/file_storage/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'tile.php', 'mods/_standard/tile_search/tile.php'), home_links=replace(home_links, 'tile.php', 'mods/_standard/tile_search/tile.php');
UPDATE `courses` SET main_links=replace(main_links, 'forum/list.php', 'mods/_standard/forums/forum/list.php'), home_links=replace(home_links, 'forum/list.php', 'mods/_standard/forums/forum/list.php');
UPDATE `courses` SET main_links=replace(main_links, 'chat/index.php', 'mods/_standard/chat/index.php'), home_links=replace(home_links, 'chat/index.php', 'mods/_standard/chat/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'faq/index.php', 'mods/_standard/faq/index.php'), home_links=replace(home_links, 'faq/index.php', 'mods/_standard/faq/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'links/index.php', 'mods/_standard/links/index.php'), home_links=replace(home_links, 'links/index.php', 'mods/_standard/links/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'tools/my_tests.php', 'mods/_standard/tests/my_tests.php'), home_links=replace(home_links, 'tools/my_tests.php', 'mods/_standard/tests/my_tests.php');
UPDATE `courses` SET main_links=replace(main_links, 'sitemap.php', 'mods/_standard/sitemap/sitemap.php'), home_links=replace(home_links, 'sitemap.php', 'mods/_standard/sitemap/sitemap.php');
UPDATE `courses` SET main_links=replace(main_links, 'export.php', 'mods/_core/imscp/export.php'), home_links=replace(home_links, 'export.php', 'mods/_core/imscp/export.php');
UPDATE `courses` SET main_links=replace(main_links, 'my_stats.php', 'mods/_standard/tracker/my_stats.php'), home_links=replace(home_links, 'my_stats.php', 'mods/_standard/tracker/my_stats.php');
UPDATE `courses` SET main_links=replace(main_links, 'polls/index.php', 'mods/_standard/polls/index.php'), home_links=replace(home_links, 'polls/index.php', 'mods/_standard/polls/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'directory.php', 'mods/_standard/directory/directory.php'), home_links=replace(home_links, 'directory.php', 'mods/_standard/directory/directory.php');
UPDATE `courses` SET main_links=replace(main_links, 'groups.php', 'mods/_core/groups/groups.php'), home_links=replace(home_links, 'groups.php', 'mods/_core/groups/groups.php');
UPDATE `courses` SET main_links=replace(main_links, 'reading_list/index.php', 'mods/_standard/reading_list/index.php'), home_links=replace(home_links, 'reading_list/index.php', 'mods/_standard/reading_list/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'blogs/index.php', 'mods/_standard/blogs/index.php'), home_links=replace(home_links, 'blogs/index.php', 'mods/_standard/blogs/index.php');
UPDATE `courses` SET main_links=replace(main_links, 'google_search/index.php', 'mods/_standard/google_search/index.php'), home_links=replace(home_links, 'google_search/index.php', 'mods/_standard/google_search/index.php');
