# Add folder node into `content` table
ALTER TABLE `content` add `content_type` tinyint NOT NULL DEFAULT 0;

# --------------------------------------------------------
# Table structure for table `content_forums_assoc`

CREATE TABLE `content_forums_assoc` (
`content_id` INTEGER UNSIGNED NOT NULL,
`forum_id` INTEGER UNSIGNED NOT NULL,
PRIMARY KEY ( `content_id` , `forum_id` )
)
ENGINE = MyISAM;

# --------------------------------------------------------
# Replace (TEXT NOT NULL) with (TEXT)
ALTER TABLE `admin_log` MODIFY `details` TEXT;

ALTER TABLE `backups` MODIFY `description` TEXT, MODIFY `file_name` TEXT, MODIFY `contents` TEXT;

ALTER TABLE `blog_posts` MODIFY `body` TEXT;

ALTER TABLE `blog_posts_comments` MODIFY `comment` TEXT;

ALTER TABLE `config` MODIFY `value` TEXT;

ALTER TABLE `content` MODIFY `keywords` TEXT, MODIFY `content_path` TEXT, MODIFY `text` TEXT, MODIFY `head` TEXT, MODIFY `test_message` TEXT;

ALTER TABLE `courses` MODIFY `description` TEXT, MODIFY `copyright` TEXT, MODIFY `home_links` TEXT, MODIFY `main_links` TEXT, MODIFY `banner` TEXT;

ALTER TABLE `faq_topics` MODIFY `name` TEXT;

ALTER TABLE `faq_entries` MODIFY `question` TEXT, MODIFY `answer` TEXT;

ALTER TABLE `files` MODIFY `description` TEXT;

ALTER TABLE `files_comments` MODIFY `comment` TEXT;

ALTER TABLE `forums` MODIFY `description` TEXT;
--
ALTER TABLE `forums_threads` MODIFY `body` TEXT;

ALTER TABLE `glossary` MODIFY `definition` TEXT;

ALTER TABLE `groups` MODIFY `description` TEXT;

ALTER TABLE `handbook_notes` MODIFY `note` TEXT;

ALTER TABLE `instructor_approvals` MODIFY `notes` TEXT;

ALTER TABLE `links` MODIFY `Description` TEXT;

ALTER TABLE `members` MODIFY `address` TEXT, MODIFY `preferences` TEXT;

ALTER TABLE `messages` MODIFY `body` TEXT;

ALTER TABLE `messages_sent` MODIFY `body` TEXT;

ALTER TABLE `news` MODIFY `body` TEXT;

ALTER TABLE `mail_queue` MODIFY `body` TEXT;

ALTER TABLE `reading_list` MODIFY `comment` TEXT;

ALTER TABLE `external_resources` MODIFY `comments` TEXT;

ALTER TABLE `tests` MODIFY `instructions` TEXT, MODIFY `description` TEXT, MODIFY `passfeedback` TEXT, MODIFY `failfeedback` TEXT;

ALTER TABLE `tests_answers` MODIFY `answer` TEXT, MODIFY `notes` TEXT;

ALTER TABLE `tests_questions` MODIFY `feedback` TEXT, MODIFY `question` TEXT, MODIFY `choice_0` TEXT
, MODIFY `choice_1` TEXT, MODIFY `choice_2` TEXT, MODIFY `choice_3` TEXT, MODIFY `choice_4` TEXT
, MODIFY `choice_5` TEXT, MODIFY `choice_6` TEXT, MODIFY `choice_7` TEXT, MODIFY `choice_8` TEXT
, MODIFY `choice_9` TEXT, MODIFY `option_0` TEXT, MODIFY `option_1` TEXT, MODIFY `option_2` TEXT
, MODIFY `option_3` TEXT, MODIFY `option_4` TEXT, MODIFY `option_5` TEXT, MODIFY `option_6` TEXT
, MODIFY `option_7` TEXT, MODIFY `option_8` TEXT, MODIFY `option_9` TEXT;

ALTER TABLE `themes` MODIFY `extra_info` TEXT;

ALTER TABLE `patches` MODIFY `description` TEXT, MODIFY `sql_statement` TEXT, MODIFY `remove_permission_files` TEXT, 
MODIFY `backup_files` TEXT, MODIFY `patch_files` TEXT;

ALTER TABLE `patches_files` MODIFY `name` TEXT;

ALTER TABLE `patches_files_actions` MODIFY `code_from` TEXT, MODIFY `code_to` TEXT;

ALTER TABLE `myown_patches` MODIFY `description` TEXT, MODIFY `sql_statement` TEXT;

ALTER TABLE `myown_patches_files` MODIFY `code_from` TEXT, MODIFY `code_to` TEXT, MODIFY `uploaded_file` TEXT;

ALTER TABLE `primary_resources` MODIFY `resource` TEXT;

ALTER TABLE `resource_types` MODIFY `type` TEXT;

ALTER TABLE `secondary_resources` MODIFY `secondary_resource` TEXT;

ALTER TABLE `fha_student_tools` MODIFY `links` TEXT;

ALTER TABLE `social_applications` MODIFY `description` TEXT, MODIFY `settings` TEXT, MODIFY `views` TEXT;

ALTER TABLE `social_application_settings` MODIFY `value` TEXT;

ALTER TABLE `social_member_position` MODIFY `description` TEXT;

ALTER TABLE `social_member_education` MODIFY `description` TEXT;

ALTER TABLE `social_privacy_preferences` MODIFY `preferences` TEXT;

ALTER TABLE `social_groups` MODIFY `description` TEXT;

ALTER TABLE `social_groups_board` MODIFY `body` TEXT;

ALTER TABLE `social_user_settings` MODIFY `app_settings` TEXT;

# added by Bologna CC. 
INSERT INTO `modules` VALUES ('_core/tool_manager', 2, 0, 0, 0, 0);

# --------------------------------------------------------
# Adding feature of content pre-requisites
# Table structure for table `content_prerequisites`
# since 1.6.4
CREATE TABLE `content_prerequisites` (
  `content_id` MEDIUMINT NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `item_id` MEDIUMINT NOT NULL,
  PRIMARY KEY (content_id,type, item_id)
) ENGINE = MyISAM;

# New Social Tables
CREATE TABLE `social_member_contact` (
  `contact_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `con_name` varchar(200) NOT NULL,
  `con_phone` varchar(15) NOT NULL,
  `con_email` varchar(50) NOT NULL,
  `con_address` text NOT NULL,
  PRIMARY KEY  (`contact_id`)
) ENGINE = MyISAM  DEFAULT CHARSET=utf8 ;


CREATE TABLE `social_member_representation` (
  `rep_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `rep_name` varchar(200) NOT NULL,
  `rep_title` varchar(50) NOT NULL,
  `rep_phone` varchar(15) NOT NULL,
  `rep_email` varchar(50) NOT NULL,
  `rep_address` text NOT NULL,
  PRIMARY KEY  (`rep_id`)
) ENGINE = MyISAM  DEFAULT CHARSET=utf8  ;

CREATE TABLE `social_member_personal` (
  `per_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `per_weight` varchar(200) NOT NULL,
  `per_height` varchar(50) NOT NULL,
  `per_hair` varchar(15) NOT NULL,
  `per_eyes` varchar(50) NOT NULL,
  `per_ethnicity` varchar(50) NOT NULL,
  `per_languages` varchar(255) NOT NULL,
  `per_disabilities` varchar(255) NOT NULL,
  PRIMARY KEY  (`per_id`)
) ENGINE = MyISAM;

# Add mobile theme
INSERT INTO `themes` VALUES ('Mobile', '1.6.4', 'mobile', NOW(), 'This is the default theme for mobile devices.', 1);
