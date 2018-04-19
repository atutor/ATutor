# DB Upgrade for ATutor 2.2.4

# Added the Helpme module as a standard module
CREATE TABLE IF NOT EXISTS `helpme_user` (
  `user_id` mediumint(8) NOT NULL,
  `help_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) SELECT '_standard/helpme', 2, 0, MAX(admin_privilege) * 2, 0, 0 FROM `modules`;

# Update db date fields for compatibility with MySQL 5.7
UPDATE `language_text` SET `revised_date` = NULL WHERE `revised_date` = '0000-00-00 00:00:00';
UPDATE `patches` SET `installed_date` = NULL WHERE `installed_date` = '0000-00-00 00:00:00';
UPDATE `myown_patches` SET `last_modified` = NULL WHERE `last_modified` = '0000-00-00 00:00:00';
UPDATE `grade_scales` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `gradebook_tests` SET `due_date` = NULL WHERE `due_date` = '0000-00-00 00:00:00';
UPDATE `oauth_client_servers` SET `create_date` = NULL WHERE `create_date` = '0000-00-00 00:00:00';
UPDATE `oauth_client_tokens` SET `assign_date` = NULL WHERE `assign_date` = '0000-00-00 00:00:00';
UPDATE `pa_albums` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `pa_albums` SET `last_updated` = NULL WHERE `last_updated` = '0000-00-00 00:00:00';
UPDATE `pa_photos` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `pa_photos` SET `last_updated` = NULL WHERE `last_updated` = '0000-00-00 00:00:00';
UPDATE `pa_album_comments` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `pa_photo_comments` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `calendar_events` SET `start` = NULL WHERE `start` = '0000-00-00 00:00:00';
UPDATE `calendar_events` SET `end` = NULL WHERE `end` = '0000-00-00 00:00:00';
UPDATE `assignments` SET `date_due` = NULL WHERE `date_due` = '0000-00-00 00:00:00';
UPDATE `assignments` SET `date_cutoff` = NULL WHERE `date_cutoff` = '0000-00-00 00:00:00';
UPDATE `content` SET `release_date` = NULL WHERE `release_date` = '0000-00-00 00:00:00';
UPDATE `courses` SET `created_date` = NULL WHERE `created_date` = '0000-00-00 00:00:00';
UPDATE `courses` SET `release_date` = NULL WHERE `release_date` = '0000-00-00 00:00:00';
UPDATE `courses` SET `end_date` = NULL WHERE `end_date` = '0000-00-00 00:00:00';
UPDATE `tests` SET `start_date` = NULL WHERE `start_date` = '0000-00-00 00:00:00';
UPDATE `tests` SET `end_date` = NULL WHERE `end_date` = '0000-00-00 00:00:00';
