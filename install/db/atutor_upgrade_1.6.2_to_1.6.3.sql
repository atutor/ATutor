# Setup tables for Social Networking module

# Activities
CREATE TABLE `social_activities` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  `title` TEXT,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Applications/ Gagdets table
CREATE TABLE `social_applications` (
  `id` INTEGER UNSIGNED,
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `height` INTEGER UNSIGNED, 
  `scrolling` INTEGER UNSIGNED,
  `screenshot` VARCHAR(255) NOT NULL,
  `thumbnail` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `author_email` VARCHAR(128) NOT NULL,
  `description` TEXT NOT NULL,
  `settings` TEXT NOT NULL,
  `views` TEXT NOT NULL,
  `last_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`url`)
)
ENGINE = MyISAM;

# Application Settings, like storing the perference string.
CREATE TABLE `social_application_settings` (
  `application_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`application_id`, `member_id`, `name`)
)
ENGINE = MyISAM;

# Application members mapping
CREATE TABLE `social_members_applications` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `application_id`)
)
ENGINE = MyISAM;

# Friends table
CREATE TABLE `social_friends` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

# Friend requests table
CREATE TABLE `social_friend_requests` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

# Person Positions (jobs)
CREATE TABLE `social_member_position` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `company` VARCHAR(255) NOT NULL,
  `from` VARCHAR(10) NOT NULL DEFAULT 0,
  `to` VARCHAR(10) NOT NULL DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Person education 
CREATE TABLE `social_member_education` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `university` VARCHAR(255) NOT NULL,
  `country` VARCHAR(128),
  `province` VARCHAR(128),
  `degree` VARCHAR(64),
  `field` VARCHAR(64),
  `from` VARCHAR(10) NOT NULL DEFAULT 0,
  `to` VARCHAR(10) NOT NULL DEFAULT 0,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Person related web sites
CREATE TABLE `social_member_websites` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `site_name` VARCHAR(255),
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Tracks visitor counts
CREATE TABLE `social_member_track` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `visitor_id` INTEGER UNSIGNED NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  PRIMARY KEY (`member_id`, `visitor_id`, `timestamp`)
)
ENGINE = MyISAM;

# Person additional information cojoint with the members table
CREATE TABLE `social_member_additional_information` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `expertise` VARCHAR(255) NOT NULL,
  `interests` TEXT,
  `associations` TEXT,
  `awards` TEXT,
  `others` TEXT,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;

# Privacy Control Preferences
CREATE TABLE `social_privacy_preferences` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `preferences` TEXT NOT NULL,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;

# Social Group tables
CREATE TABLE `social_groups` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `type_id` INTEGER UNSIGNED NOT NULL,
  `privacy` INTEGER UNSIGNED NOT NULL,
   `name` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `created_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_activities` (
  `activity_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`activity_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_members` (
  `group_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `member_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_invitations` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_requests` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_types` (
  `type_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(127) NOT NULL,
  PRIMARY KEY (`type_id`)
)
ENGINE = MyISAM;

# CREATE TABLE `social_groups_forums` (
#   `group_id` INTEGER UNSIGNED NOT NULL,
#   `forum_id` INTEGER UNSIGNED NOT NULL,
#   PRIMARY KEY (`group_id`, `forum_id`)
# )
# ENGINE = MyISAM;

# Groups message board
CREATE TABLE `social_groups_board` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  `created_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


# Settings
CREATE TABLE `social_user_settings` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_settings` TEXT NOT NULL,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;


#====== Initial Data ========
INSERT INTO social_groups_types SET title='business', type_id=1;
INSERT INTO social_groups_types SET title='common_interest', type_id=2;
INSERT INTO social_groups_types SET title='entertainment_arts', type_id=3;
INSERT INTO social_groups_types SET title='geography', type_id=4;
INSERT INTO social_groups_types SET title='internet_technology', type_id=5;
INSERT INTO social_groups_types SET title='organization', type_id=6;
INSERT INTO social_groups_types SET title='music', type_id=7;
INSERT INTO social_groups_types SET title='sports_recreation', type_id=8;

# END Social Networking setup

# Module setting
INSERT INTO `modules` VALUES ('_standard/social',	 2, 8388608, 0, 0, 0);

# Login attempt control table
CREATE TABLE `member_login_attempt` (
  `login` varchar(20) NOT NULL,
  `attempt` tinyint(3) unsigned default NULL,
  `expiry` int(10) unsigned default NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM;

# --------------------------------------------------------
# Adding feature of blog subsription
# Table structure for table `blog_subscription`
# since 1.6.3
CREATE TABLE `blog_subscription` (
  `group_id` MEDIUMINT NOT NULL ,
  `member_id` MEDIUMINT NOT NULL ,
  PRIMARY KEY (group_id,member_id)
) ENGINE=MyISAM;

# END Adding feature of blog subsription

# --------------------------------------------------------
# Adding feature of "detail view" and "icon view" on course home page
# since 1.6.3
ALTER TABLE `courses` add `home_view` tinyint NOT NULL DEFAULT 0;
ALTER TABLE `fha_student_tools` add `home_view` tinyint NOT NULL DEFAULT 0;

# END Adding feature of "detail view" and "icon view" on course home page
