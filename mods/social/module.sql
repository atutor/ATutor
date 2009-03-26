# Setup Table for ATutor Social Networking Feature
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
  `screenshot` VARCHAR(255) NOT NULL,
  `thumbnail` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `author_email` VARCHAR(128) NOT NULL,
  `description` TEXT NOT NULL,
  `settings` TEXT NOT NULL,
  `views` TEXT NOT NULL,
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
  `from` INTEGER(6) NOT NULL DEFAULT 0,
  `to` INTEGER(6) NOT NULL DEFAULT 0,
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
  `from` INTEGER(4) NOT NULL DEFAULT 0,
  `to` INTEGER(4) NOT NULL DEFAULT 0,
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
   `name` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `created_date` TIMESTAMP NOT NULL,
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

CREATE TABLE `social_groups_types` (
  `type_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(127) NOT NULL,
  PRIMARY KEY (`type_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_forums` (
  `group_id` INTEGER UNSIGNED NOT NULL,
  `forum_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `forum_id`)
)
ENGINE = MyISAM;

# Groups message board
CREATE TABLE `social_groups_board` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  `body` TEXT NOT NULL,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;



#====== Initial Data ========
REPLACE INTO at_social_groups_types SET title='business', type_id=1;
REPLACE INTO at_social_groups_types SET title='common_interest', type_id=2;
REPLACE INTO at_social_groups_types SET title='entertainment_arts', type_id=3;
REPLACE INTO at_social_groups_types SET title='geography', type_id=4;
REPLACE INTO at_social_groups_types SET title='internet_technology', type_id=5;
REPLACE INTO at_social_groups_types SET title='organization', type_id=6;
REPLACE INTO at_social_groups_types SET title='music', type_id=7;
REPLACE INTO at_social_groups_types SET title='sports_recreation', type_id=8;

