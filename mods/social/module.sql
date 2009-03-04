# Setup Table for ATutor Social Networking Feature
# Activities
CREATE TABLE `activities` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  `title` TEXT,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Applications/ Gagdets table
CREATE TABLE `applications` (
  `id` INTEGER UNSIGNED,
  `url` TEXT NOT NULL NOT NULL DEFAULT '',
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
CREATE TABLE `application_settings` (
  `application_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`application_id`, `member_id`, `name`)
)
ENGINE = MyISAM;

# Friends table
CREATE TABLE `friends` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

# Friend requests table
CREATE TABLE `friend_requests` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

# Person Positions (jobs)
CREATE TABLE `member_position` (
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
CREATE TABLE `member_education` (
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
CREATE TABLE `member_websites` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `site_name` VARCHAR(255),
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Person additional information cojoint with the members table
CREATE TABLE `member_additional_information` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `interests` TEXT,
  `associations` TEXT,
  `awards` TEXT,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;

# Privacy Control Preferences
CREATE TABLE `privacy_preferences` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `preferences` TEXT NOT NULL,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;
