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
REPLACE INTO social_groups_types SET title='business', type_id=1;
REPLACE INTO social_groups_types SET title='common_interest', type_id=2;
REPLACE INTO social_groups_types SET title='entertainment_arts', type_id=3;
REPLACE INTO social_groups_types SET title='geography', type_id=4;
REPLACE INTO social_groups_types SET title='internet_technology', type_id=5;
REPLACE INTO social_groups_types SET title='organization', type_id=6;
REPLACE INTO social_groups_types SET title='music', type_id=7;
REPLACE INTO social_groups_types SET title='sports_recreation', type_id=8;

# Module Language
INSERT INTO `language_text` VALUES ('en', '_module','settings','Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','social','Networking',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','applications','Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','manage_applications','Manage Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_application','Add Gadget',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','show_available_applications','Show Available Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','network_updates','Network Activity',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','connections','My Contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','no_activities','No network activity',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','no_friends','You have no contacts yet.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pending_friend_requests','Pending Friend Requests',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','profile_picture','Profile Picture',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','approve_request','Approve_request?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','accept_request','Accept request?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_for_friends','Search for contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','my_connections','Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','there_are_entries','There are %s entries.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','remove_friend','Remove Contact',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_to_friends','Add to contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','my_friends_only','Only my contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','expertise','Expertise',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','dob','Date of Birth',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','interests','Interests',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','associations','Associations',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','awards','Awards',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','others','Others',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','training_and_education','Training and Education',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','credits_and_work_experience','Credits and Work Experience',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','institution','School/Institution',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','degree','Degree/Program/Courses',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','year','Year',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','company','Company',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','title','Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','activities','Activities',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','edit_profile','Edit Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','has_added_app','has added the <a href="%s">%s</a>  gadget',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','gadgets','Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_application_url','Add gadget by URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','your_applications','My Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','social_profile','Network Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','social_groups','Network Groups',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','recently_joined','Recently Joined',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','my_groups','My Network Groups',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','group_name','Group Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','group_type','Group Type',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','group_logo','Group Logo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','business','Business',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','common_interest','Common Interest',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','entertainment_arts','Arts and Entertainment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','geography','Geographic',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','internet_technology','Internet Technology',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','music','Music',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','sports_recreation','Sports and Recreation',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','group_info','Group Details',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','created_by','Created By',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','last_update','Last Update',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','number_of_members','Number of Members',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','message_board','Message Board',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','invite','Invite',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','leave_group','Leave Group',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','edit_group','Edit Group',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','disband_group','Disband Group',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','added_member','Added Members',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','not_added_member','Add Members',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','invite_group','Invite New Group Members',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','position','Position',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_position','Add new position',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','education','Education',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_education','Add new education',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','websites','Websites',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_websites','Add new website',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_interest','Add new interest',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_association','Add new association',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_new_award','Add new award',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','reject_request','Reject Request',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','show_your_applications','Show Your Applications',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','avaiable_applications','Available Gadgets',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','view_groups','View Group',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','now_friends1','and %s are now contacts.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','now_friends2','and %s are now contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','friends','My Contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','friends_of_friends','Contacts of Your Contacts',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','local_network','Local Network',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','world_network','World Network',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basic_profile','Basic Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','detailed_profile','Detailed Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','profile_control','Profile Visability',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','privacy_control_blurb','Controls who can see your profile and related information.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_visibility','Search Visibility',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_profile','Search Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_connections','Search Connections',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_education','Search Education',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','search_position','Search Position',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','privacy_settings','Privacy Settings',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CANNOT_BE_EMPTY','Search field cannot be empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_GADGET_ADDED_SUCCESSFULLY','Gadget was successsfully added.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_GADGET_REMOVED_SUCCESSFULLY','Gadget was successsfully removed.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_GROUP_CREATED','Group was successfully created.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_GROUP_CREATION_FAILED','Group creation failed.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_GROUP_JOINED','Group successfully joined.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_INVITATION_SENT','Invitation successfully sent.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CANT_DELETE_GROUP','You cannot delete this group.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_GROUP_HAS_BEEN_REMOVED','Group has been removed.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_SOCIAL_GROUP_UPDATED','Group successfully updated.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_GROUP_EDIT_FAILEDY','Group edit failed.',NOW(),'');



