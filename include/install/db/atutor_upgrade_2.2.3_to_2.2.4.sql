# DB Upgrade for ATutor 2.2.4

# Added the Helpme module as a standard module
CREATE TABLE IF NOT EXISTS `helpme_user` (
  `user_id` mediumint(8) NOT NULL,
  `help_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


REPLACE INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) SELECT '_standard/helpme', 2, 0, MAX(admin_privilege) * 2, 0, 0 FROM `modules`;

// Add Gameme as a standard Module
DO SLEEP(3);
REPLACE INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) SELECT '_standard/gameme', 2, MAX(privilege) * 2, MAX(admin_privilege) * 2, 0, 0 FROM `modules`;

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



CREATE TABLE IF NOT EXISTS `gm_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `image_url` varchar(96) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `gm_badges` WRITE;
/*!40000 ALTER TABLE `AT_gm_badges` DISABLE KEYS */;

INSERT INTO `gm_badges` (`id`, `course_id`, `alias`, `title`, `description`, `image_url`)
VALUES
	(7,0,'upload_file_badge','Good use of File Storage','You have figured out how to upload files into the course.','mods/_standard/gameme/images/badges/arrow.png'),
	(8,0,'create_file_badge','Create your own files','You learned how to create new files in File Storage.','mods/_standard/gameme/images/badges/doc.png'),
	(2,0,'profile_viewed_badge','You\'re getting noticed','25 people have viewed your profile','mods/_standard/gameme/images/badges/eye.png'),
	(1,0,'profile_view_badge','You know your classmates','You have viewed 25 of your classmates\' profiles','mods/_standard/gameme/images/badges/id.png'),
	(4,0,'prefs_update_badge','You found your settings','You know how to update your personal preference, and configure ATutor to your liking. ','mods/_standard/gameme/images/badges/mixer.png'),
	(3,0,'profile_pic_upload_badge','You have a profile pic','People are more likely to interact when you have a profile picture.','mods/_standard/gameme/images/badges/adduser.png'),
	(5,0,'read_page_badge','You are well on your way','You have read 25 pages in the course. Keep going!','mods/_standard/gameme/images/badges/silver.png'),
	(6,0,'new_folder_badge','You\'re organized','You know how to create folder in File Storage to organize your files.','mods/_standard/gameme/images/badges/folder.png'),
	(9,0,'forum_view_badge','Discussion Reader','You are doing a great job reading through discussion posts in the forums.','mods/_standard/gameme/images/badges/bronze.png'),
	(10,0,'forum_post_badge','Discussion Poster','You have been a great contributor in the discussion forums.','mods/_standard/gameme/images/badges/gold.png'),
	(11,0,'forum_reply_badge','Great Feedback','You have been replying to others posts in the discussion forums','mods/_standard/gameme/images/badges/conversation.png'),
	(12,0,'blog_add_badge','Blog Poster','You\'re making great use of the course blog. Keep on posting!','mods/_standard/gameme/images/badges/email.png'),
	(13,0,'blog_comment_badge','Blog Commenter','You have been commenting on other (or your own) blog posts. Keep on commenting.','mods/_standard/gameme/images/badges/lightbulb.png'),
	(14,0,'chat_login_badge','Chat Login','You are making good use of the ATutor chat, a great place to interact live with your classmates','mods/_standard/gameme/images/badges/chat.png'),
	(15,0,'chat_post_badge','Chat Contributor','You are posting message to the chat. Keep on chatting!','mods/_standard/gameme/images/badges/bolt.png'),
	(16,0,'link_add_badge','Link Poster','You\'ve been adding links to the course resources. Keep adding!','mods/_standard/gameme/images/badges/link.png'),
	(17,0,'photo_create_album_badge','Create Album','You learned how to create an album in the Photo Gallery. Keep creating albums to share.','mods/_standard/gameme/images/badges/news.png'),
	(18,0,'photo_create_album_badge','Create Albums','You have created several photo albums. Perhaps photograhpy is your calling!','mods/_standard/gameme/images/badges/brush.png'),
	(19,0,'photo_upload_badge','Photo Uploader','You have been uploading photos into your photo gallery. Keep adding.','mods/_standard/gameme/images/badges/picture.png'),
	(20,0,'photo_comment_badge','Photo comments','You have been commenting you yours and others photos. Keep commenting for bonus points;','mods/_standard/gameme/images/badges/like.png'),
	(21,0,'photo_album_comment','Album Comment','Most people comment on photo, but you commenteed on an album for bonus points.','mods/_standard/gameme/images/badges/cards.png'),
	(22,0,'photo_description_badge','Photo Describer','Exellent job providing descriptions for you photos. ','mods/_standard/gameme/images/badges/feather.png'),
	(23,0,'photo_alt_text','Accessibility Aware','Its great you are providing Alt text for you image, to make them accessible to people with disabilities. Secret bonus points if you continue adding Alt text to new images in your gallery.','mods/_standard/gameme/images/badges/heart.png'),
	(24,0,'login_badge','Returning Visitor','You have come back quite a few times now. Keep on visiting the course for bonus points.','mods/_standard/gameme/images/badges/hot.png'),
	(25,0,'logout_badge','Security Conscious','You have been logging out, rather than leaving or allowing your session to time out. This helps improve security.','mods/_standard/gameme/images/badges/lock.png'),
	(26,0,'welcome_badge','Welcome','Welcome to the course. Finding your way here earned you your first badge. Get busy with the course to earn points and collect more badges.','mods/_standard/gameme/images/badges/acorn.png');

UNLOCK TABLES;


CREATE TABLE IF NOT EXISTS `gm_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `description` text,
  `allow_repetitions` tinyint(1) DEFAULT '1',
  `reach_required_repetitions` int(11) DEFAULT NULL,
  `max_points` int(11) DEFAULT NULL,
  `id_each_badge` int(11) DEFAULT NULL COMMENT '	',
  `id_reach_badge` int(11) DEFAULT NULL,
  `each_points` int(11) DEFAULT NULL,
  `reach_points` int(11) DEFAULT NULL,
  `each_callback` varchar(64) DEFAULT NULL,
  `reach_callback` varchar(64) DEFAULT NULL,
  `reach_message` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `gm_events` WRITE;


INSERT INTO `gm_events` (`id`, `course_id`, `alias`, `description`, `allow_repetitions`, `reach_required_repetitions`, `max_points`, `id_each_badge`, `id_reach_badge`, `each_points`, `reach_points`, `each_callback`, `reach_callback`, `reach_message`)
VALUES
	(2,0,'profile_view','Profile view other\'s',0,10,NULL,NULL,1,10,25,NULL,'GmCallbacksClass::ProfileViewReachCallback','Congratulations, you have received a new badge for getting to know your classmates by viewing their profiles. You can earn additional points by sending a private message to a person through their profile page.'),
	(3,0,'profile_viewed','Profile viewed by others',0,25,NULL,NULL,2,25,50,NULL,'GmCallbacksClass::ProfileViewedReachCallback','Congratulations, you have received a new badge because lots of people have been viewing your profile.'),
	(4,0,'sent_message','Send a private message',0,10,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(5,0,'profile_pic_upload','Upload a profile picture',0,1,NULL,NULL,3,100,200,NULL,'GmCallbacksClass::ProfilePicUploadCallback','Congratulations, you have received a new badge for adding a profile picture. Update your profile picture occassionally to receive additional points.'),
	(6,0,'read_list_view','View reading list details',0,15,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(7,0,'prefs_update','Update personal preferences',0,1,NULL,NULL,4,25,250,NULL,'GmCallbacksClass::PreferencesUpdateCallback','Congratulations, you have received a new badge for updating your personal preferences.'),
	(8,0,'read_page','Pages viewed',0,25,NULL,NULL,5,10,25,NULL,'GmCallbacksClass::ReadPageCallback','Congratulations, you have received a new badge for getting a good amount of course reading done!'),
	(9,0,'new_folder','Create file storage folder',0,1,NULL,NULL,6,25,100,NULL,'GmCallbacksClass::FileStorageFolderCallback','Congratulations, you have received a new badge for learning how to create folders to organize your files. You can also earn points and badges by adding files to those folders'),
	(10,0,'upload_file','Upload to file storage',0,5,NULL,NULL,7,25,50,NULL,'GmCallbacksClass::UploadFilesCallback','Congratulations, you have received a new badge for learning how to use file storage to store your files. Create additional folders to organize your files for additional points and badges.'),
	(11,0,'create_file','Create file in file storage',0,2,NULL,NULL,8,50,100,NULL,'GmCallbacksClass::CreateFilesCallback','Congratulations, you have received a new badge for learning how to create new files in file storage.'),
	(12,0,'file_comment','Comment on a file storage file',0,5,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(13,0,'file_description','Provide description for file storage file',0,5,NULL,NULL,NULL,50,100,NULL,NULL,NULL),
	(14,0,'forum_view','Forum discussions viewed',0,25,NULL,NULL,9,25,150,NULL,'GmCallbacksClass::ForumViewCallback','Congratulations, you have received a new badge for keeping up with reading forum posts. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(15,0,'forum_post','Forum posts',0,10,NULL,NULL,10,50,100,NULL,'GmCallbacksClass::ForumPostsCallback','Congratulations, you have received a new badge for contributing new threads to the discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(16,0,'forum_reply','Forum replies',0,5,NULL,NULL,11,75,150,NULL,'GmCallbacksClass::ForumReplyCallback','Congratulations, you have received a new badge for contributing good feedback to discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(17,0,'read_time','Page view time',0,10,NULL,NULL,NULL,25,100,NULL,NULL,NULL),
	(18,0,'blog_add','Blob posts',0,10,NULL,NULL,12,25,100,NULL,'GmCallbacksClass::BlogAddCallback','Congratulations, you have received a new badge for contributing a good collection of blog posts. Continue adding to your blog, and comments on others\' blogs to earn additional points and badges.'),
	(19,0,'blog_comment','Blog comments',0,2,NULL,NULL,13,25,100,NULL,'GmCallbacksClass::BlogCommentsCallback','Congratulations, you have received a new badge for contributing good feedback, and commenting on blog posts. Continue posting to your blog, and commenting on others\' blog posts to earn additional points.'),
	(20,0,'blog_view','Blog views',0,15,NULL,NULL,NULL,15,50,NULL,NULL,NULL),
	(21,0,'blog_post_view','Blog posts viewed',0,10,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(22,0,'chat_login','Chat login',0,10,NULL,NULL,14,5,100,NULL,'GmCallbacksClass::ChatLoginCallback','Congratulations, you have received a new badge for logging into the chat regularly. Just using the chat helps accumulate points.'),
	(23,0,'chat_post','Chat posts',0,50,NULL,NULL,15,5,100,NULL,'GmCallbacksClass::ChatPostCallback','Congratulations, you have received a new badge for keeping conversation going in the chat room. Returning to the chat room regularly earns additional points.'),
	(24,0,'link_add','Links added',0,2,NULL,NULL,16,25,50,NULL,'GmCallbacksClass::LinkAddCallback','Congratulations, you have received a new badge for making a good contribution to the course links. View links others have posted to earn additional points.'),
	(25,0,'link_view','Links followed',0,15,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(26,0,'poll_post','Polls posted',0,2,NULL,NULL,NULL,25,75,NULL,NULL,NULL),
	(27,0,'photo_create_album','Photo album created',1,1,NULL,17,NULL,50,100,NULL,'GmCallbacksClass::PhotoAlbumCallback','Congratulations, you have received a new badge for creating a photo album. Continue adding photos to earn more points and badges.'),
	(28,0,'photo_upload','Photo uploads',0,10,NULL,NULL,19,25,50,NULL,'GmCallbacksClass::PhotoUploadCallback','Congratulations, you have received a new badge for uploading a good collection of photos. Continue adding photos to earn more points. Create additional albums to organize your photos for bonus points.'),
	(29,0,'photo_view_album','View photo album',0,5,NULL,NULL,NULL,10,30,NULL,NULL,NULL),
	(30,0,'photo_view_photo','View photo',0,25,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(31,0,'photo_comment','Comment on a photo',0,2,NULL,NULL,20,25,75,NULL,'GmCallbacksClass::PhotoCommentCallback','Congratulations, you have received a new badge for providing comments on yours, and others photos. Continue commenting to earn additional points. You can also comment on photo albums as a whole, to earn bonus points.'),
	(32,0,'photo_album_comment','Comment on an album',0,5,NULL,NULL,21,50,150,NULL,'GmCallbacksClass::PhotoAlbumCommentCallback','Congratulations, you have received a new badge for providing comments on your\'s, and other\'s albums. Continue commenting about albums for additional points.'),
	(33,0,'photo_description','Photo descriptions provided',0,5,NULL,NULL,22,25,150,NULL,'GmCallbacksClass::PhotoDescriptionCallback','Congratulations, you have received a new badge for providing descriptions for your photos. Add alternative text to make your photos accessible to blind classmates, and earn bonus points and a badge.'),
	(34,0,'photo_alt_text','Photo Alt texts provided',0,2,NULL,NULL,23,50,250,NULL,'GmCallbacksClass::PhotoAltTextCallback','Congratulations, you have received a new badge for providing alternative text for your photos. This makes photos accessible to blind classmates using a screen reader to access the course. Providing descriptions for your photos can also earn points, and a badge.'),
	(35,0,'photo_create_albums','Photo albums created',0,3,NULL,NULL,18,50,100,NULL,'GmCallbacksClass::PhotoAlbumsCallback','Congratulations, you have received a new badge for creating multiple photo albums to organize your photos. Continue adding photos to earn more points.'),
	(38,0,'logout','Logout (not timeout)',0,2,250,NULL,25,10,25,NULL,'GmCallbacksClass::LogoutReachCallback','Congratulations, you have received a new badge for logging out properly, instead of leaving or letting your session timeout, maintaining your privacy and security. '),
	(39,0,'welcome','First course login',1,1,250,NULL,26,250,NULL,NULL,'GmCallbacksClass::WelcomeCallback','Welcome to the course. You have earned your first badge by successfully logging in. Continue earning badges by using the features in the course, and participating in course activities.<br /><br />By participating in the course you can also earn points and advance through levels as your points grow. Follow the leader board to see your position among others in the course. Watch for hints after earning a badge, for earning additional badges and bonus points.'),
	(1,0,'login','Login',0,25,NULL,NULL,24,10,100,NULL,'GmCallbacksClass::LoginReachCallback','Congratulations, you have received a new badge for logging into the course many times. You can also earn points by logging out of the course properly, clicking the logout link, instead of just leaving or letting your session timeout.'),
	(37,0,'submit_test','Submit a test or quiz',0,5,NULL,NULL,NULL,100,250,NULL,NULL,NULL);

UNLOCK TABLES;


CREATE TABLE IF NOT EXISTS `gm_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `points` int(11) NOT NULL,
  `icon` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `gm_options` (
`id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `gm_option` varchar(25) NOT NULL DEFAULT '',
  `value` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`course_id`,`gm_option`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_alerts` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_badges` (
  `id_user` int(10) unsigned NOT NULL,
  `id_badge` int(10) unsigned NOT NULL,
  `badges_counter` int(10) unsigned NOT NULL,
  `grant_date` datetime NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_badge`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_events` (
  `id_user` int(10) unsigned NOT NULL,
  `id_event` int(10) unsigned NOT NULL,
  `event_counter` int(10) unsigned NOT NULL,
  `points_counter` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_event`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_logs` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_event` int(10) unsigned DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL,
  `points` int(10) unsigned DEFAULT NULL,
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_scores` (
  `id_user` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `id_level` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#### Populate Gameme tables with default data

CREATE TABLE IF NOT EXISTS `gm_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `points` int(11) NOT NULL,
  `icon` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `gm_levels` (`id`, `course_id`, `title`, `description`, `points`, `icon`)
VALUES
	(1,0,'Level 0','Welcome to the course',0,'star_empty_lg.png'),
	(2,0,'Level 1','1000 points passed',1000,'star_white_lg.png'),
	(3,0,'Level 2','2500 points passed',2500,'star_yellow_lg.png'),
	(4,0,'Level 3','5000 points passed',5000,'star_red_lg.png'),
	(5,0,'Level 4','7500 points passed',7500,'star_green_lg.png'),
	(6,0,'Level 5','10000 points passed: ',10000,'star_blue_lg.png'),
	(7,0,'Level 6','20000 points passed',20000,'star_black_lg.png'),
	(8,0,'Level 7','25000 points passed: Accomplished status, Bronze Badge',25000,'star_bronze_lg.png'),
	(9,0,'Level 8','35000 point passed: Intermediate status, Silver Badge',35000,'star_silver_lg.png'),
	(10,0,'Level 9','50000 points passed: Advanced status: Gold Badge',50000,'star_gold_lg.png'),
	(11,0,'Level 10','65000 point passed: Highest Honor: Platinum Badge',65000,'star_platinum_lg.png');

CREATE TABLE IF NOT EXISTS `gm_options` (
`id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `gm_option` varchar(25) NOT NULL DEFAULT '',
  `value` int(11) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_alerts` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_badges` (
  `id_user` int(10) unsigned NOT NULL,
  `id_badge` int(10) unsigned NOT NULL,
  `badges_counter` int(10) unsigned NOT NULL,
  `grant_date` datetime NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_events` (
  `id_user` int(10) unsigned NOT NULL,
  `id_event` int(10) unsigned NOT NULL,
  `event_counter` int(10) unsigned NOT NULL,
  `points_counter` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_logs` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_event` int(10) unsigned DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL,
  `points` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_scores` (
  `id_user` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `id_level` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `gm_options`
 ADD PRIMARY KEY (`course_id`,`gm_option`), ADD KEY `id` (`id`);

ALTER TABLE `gm_options`
 CHANGE `option` "gm_option" VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT; 

ALTER TABLE `gm_user_badges`
 ADD PRIMARY KEY (`id_user`,`id_badge`,`course_id`);

ALTER TABLE `gm_user_events`
 ADD PRIMARY KEY (`id_user`,`id_event`,`course_id`);

ALTER TABLE `gm_user_logs`
 ADD KEY `id_user` (`id_user`);

ALTER TABLE `gm_user_scores`
 ADD PRIMARY KEY (`id_user`,`course_id`);

ALTER TABLE `gm_badges`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;

ALTER TABLE `gm_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=40;

ALTER TABLE `gm_levels`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;

ALTER TABLE `gm_options`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=410;
