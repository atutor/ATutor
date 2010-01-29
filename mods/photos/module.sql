# Photo Album Table
CREATE TABLE `pa_albums` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `permission` VARCHAR(45) NOT NULL,
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


# Languages Varaibles
INSERT INTO `language_text` VALUES ('en', '_module','albums','Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','photo','Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','profile_gallery','Profile Gallery',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','edit_photos','Edit Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','organize_photos','Organize Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_more_photos','Add More Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','upload_blurb','Click "Add More Photo" and browse for the picture that you wished to upload.  These photos will be processed and displayed below.  You also have the option to remove the pending photos anytime.  When you are done, click "Upload".',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_name','Album Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_type','Album Type',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_location','Album Location',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_description','Album Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_photos','Album Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','album_cover','Album Cover',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','my_albums','My Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','course_albums','Course Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','create_album','Create Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','alt_text','Alternative Text',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','no_album','No Album Available',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','edit_photo','Edit Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','delete_this_photo','Delete This Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','organize_photo_blurb','Note: Drag photos using a mouse, or [CTRL]+[Left/Right/Up/Down Arrow] keys to rearrange them.',NOW(),'');
