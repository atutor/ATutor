###############################################################
# Database upgrade SQL from ATutor 1.4 to ATutor 1.4.1
###############################################################

# Table structure for table `polls`
CREATE TABLE `polls` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` MEDIUMINT UNSIGNED NOT NULL ,
  `question` VARCHAR( 100 ) NOT NULL ,
  `created_date` DATETIME NOT NULL ,
  `total` SMALLINT UNSIGNED NOT NULL ,
  `choice1` VARCHAR( 100 ) NOT NULL ,
  `count1` SMALLINT UNSIGNED NOT NULL ,
  `choice2` VARCHAR( 100 ) NOT NULL ,
  `count2` SMALLINT UNSIGNED NOT NULL ,
  `choice3` VARCHAR( 100 ) NOT NULL ,
  `count3` SMALLINT UNSIGNED NOT NULL ,
  `choice4` VARCHAR( 100 ) NOT NULL ,
  `count4` SMALLINT UNSIGNED NOT NULL ,
  `choice5` VARCHAR( 100 ) NOT NULL ,
  `count5` SMALLINT UNSIGNED NOT NULL ,
  `choice6` VARCHAR( 100 ) NOT NULL ,
  `count6` SMALLINT UNSIGNED NOT NULL ,
  `choice7` VARCHAR( 100 ) NOT NULL ,
  `count7` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY ( `poll_id` ) ,
  INDEX ( `course_id` )
) TYPE=MyISAM;

# --------------------------------------------------------

# Table structure for table `polls_members`

CREATE TABLE `polls_members` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL ,
  `member_id` MEDIUMINT UNSIGNED NOT NULL ,
  PRIMARY KEY ( `poll_id` , `member_id` )
) TYPE=MyISAM;


# Change age to date of birth 
ALTER TABLE `members` CHANGE `age` `dob` DATE NOT NULL;

# Add `primary_language` to the `courses` table
ALTER TABLE `courses` ADD `primary_language` VARCHAR( 4 ) DEFAULT 'en' NOT NULL;
