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

# Languages Varaibles
INSERT INTO `language_text` VALUES ('en', '_module','photos','Photo Gallery',NOW(),''); #For admin
INSERT INTO `language_text` VALUES ('en', '_module','pa_photo_gallery','Photo Gallery',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_albums','Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_photo','Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_photos','Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_profile_album','Profile Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_edit_photos','Edit Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_edit_photo','Edit Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_edit_album','Edit Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_delete_album','Delete Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_delete_comment','Delete Comment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_delete_photo','Delete Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_delete_this_photo','Delete This Photo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_organize_photos','Organize Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_add_more_photos','Add More Photos',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_upload_blurb','Click "Add More Photo" and browse for the picture that you wished to upload.  These photos will be processed and displayed below.  You also have the option to remove the pending photos anytime.  When you are done, click "Upload".',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_album_name','Album Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_album_type','Album Type',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_album_location','Album Location',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_album_description','Album Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_album_cover','Album Cover',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_last_updated','Last Updated',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_my_albums','My Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_course_albums','Course Albums',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_create_album','Create Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_alt_text','Alternative Text',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_processed','Processed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_no_album','No Album Available',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_no_photos','No Photos Available',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_organize_photo_blurb','Note: Drag photos using a mouse, or [CTRL]+[Left/Right/Up/Down Arrow] keys to rearrange them.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_of','of',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_undo','Undo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_redo','Redo',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_click_here_to_edit','Click here to edit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_click_item_to_edit','Click item to edit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_write_a_comment','Write a comment...',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_preferences','Album Preferences',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_max_memory','Maximum Memory Size allowed per member',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_memory_usage','Memory Usage',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_open_upload_manager','Open Upload Manager',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_close_upload_manager','Close Upload Manager',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','pa_no_image','No image',NOW(),'');

# Error messages
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_ADD_COMMENT_FAILED','Comment could not be added due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_EMPTY_COMMENT','Comment can not be empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_ADD_PHOTO_FAILED','Photo could not be added due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_CREATE_ALBUM_FAILED','Album could not be created due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_EDIT_ALBUM_FAILED','Album could not be edited due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_EDIT_PHOTO_FAILED','Photo could not be edited due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_EMTPY_ALBUM_NAME','Album name can not be empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_PHOTO_NOT_FOUND','Photo can not be found.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_MEMORY_INPUT_ERROR','Invalid input.  Please enter a valid Integer.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_MEMORY_SQL_ERROR','Preferences were not updated due to an internal error.  Please try again.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PA_EXCEEDED_MAX_USAGE','You have exceeded the maximum allowable memory usage for the photo album.',NOW(),'');

# Confirm messages
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_PA_DELETE_ALBUM','Are you sure you want to delete the album <strong>%s</strong>? Once deleted, photos can not be recovered.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_PA_DELETE_PHOTO','Are you sure you want to delete this Photo?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_PA_DELETE_COMMENT','Are you sure you want to delete this comment?',NOW(),'');
