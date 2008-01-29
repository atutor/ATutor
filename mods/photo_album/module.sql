# sql file for the photo album module
# this file loads the text used in the photo album module into the database

CREATE TABLE `pa_image` (
	`course_id` mediumint(8) NOT NULL,
	`image_id` mediumint(8) NOT NULL auto_increment,
	`title` varchar(23) NOT NULL,
	`description` text,
	`view_image_name` varchar(255) NOT NULL,
	`location` text NOT NULL,
	`date` varchar(30) NOT NULL,
	`login` varchar(20) NOT NULL,
	`thumb_image_name` varchar(255) NOT NULL,
	`alt` varchar(30) NOT NULL,
	`status` mediumint(1) NOT NULL,
	PRIMARY KEY (`image_id`)
);

CREATE TABLE `pa_comment` (
	`course_id` mediumint(8) NOT NULL,
	`login` varchar(20) NOT NULL,
	`comment` text,
	`date` varchar(30) NOT NULL,
	`image_id` mediumint(8) NOT NULL,
	`comment_id` mediumint(8) NOT NULL auto_increment,
	`status` mediumint(1) NOT NULL,
	PRIMARY KEY (`comment_id`)
);

CREATE TABLE `pa_config` (
	`config_id` mediumint(8) NOT NULL auto_increment,
	`date` varchar(30) NOT NULL,
	`status` mediumint(1) NOT NULL,
	`course_id` mediumint(8) NOT NULL,
	PRIMARY KEY (`config_id`)
);

INSERT INTO `language_text` VALUES ('en', '_module','photo_album','Photo Album',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_view_image_link', 'View this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_view_comment_link', 'View this comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_not_approved', 'Picture not approved by the instructor', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_check_all_image', 'Check all displayed pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_check_all_comment', 'Check all displayed comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_my_photo_alt', 'My photo album view', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_my_comment_alt', 'My comment album view', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_course_photo_alt', 'Course photo album view', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_go', 'Go', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_alt', 'Alt', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_description', 'Picture description', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_title', 'Title ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_comment_description', 'Comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_delete_confirm', 'Do you <strong>really</strong> want to <strong>delete</strong> this picture?', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_comment_delete_confirm', 'Do you <strong>really</strong> want to <strong>delete</strong> this comment?', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_add_alt', 'Picture to add', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_edit_alt', 'Picture to edit', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_title2', 'Picture title *', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_description2', 'Picture description', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_image_alt', 'Alt string', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_new_pic', 'New pictures ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_approved_pic', 'Approved pictures ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_disapproved_pic', 'Disapproved pictures ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_new_comment', 'New comments ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_approved_comment', 'Approved comments ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_disapproved_comment', 'Disapproved comments ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_config_enabled', 'Enabled - submissions are moderated ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_config_disabled', 'Disabled - submissions are posted immediately ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_config_string', 'Enable moderation ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_administrator', 'Administrator ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_next_page_button', 'Next page', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_last_page_button', 'Last page', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_previous_page_button', 'Previous page', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_first_page_button', 'First page', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_tag_view', 'View : ', NOW(), '');

INSERT INTO `language_text` VALUES ('en', '_template', 'pa_label_file', 'Picture file name', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_label_pic_title', 'Picture title ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_label_pic_description', 'Picture description ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_label_pic_alt', 'Alt string', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_label_comment_textarea', 'Comment area', NOW(), '');

INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_admin', 'Please select a course from the drop down menu below to administer its photo album. <br/> Take note that photo albums are attached to courses and that pictures are not shared across courses. ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_file_upload_add', 'To add a picture, click the "Browse" button to find a picture to upload, then click the "upload this picture" button', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_file_upload_edit', 'You can upload a new picture by clicking the "Browse" button to find a picture then clicking the "Update this picture" button or if you do not want to change the picture, just click the "Skip this step" button. ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_image_info_add', 'Please give your picture a title and a description by filling in the form', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_comment_add', 'Leave a comment about this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_comment_edit', 'Change your comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_image_info_edit', 'You can edit the picture title and description by making changes to the form below', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_instructor_config', 'Do you want to moderate submissions to your course album? If you want to approve pictures and comments before they are viewed publicly, click the radio button labelled "Yes" to activate the approval process. If you want pictures and comments posted immediately, click "No". Select either radio button then click the "change approval process" button to save your setting.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_administrator_config', 'Do you want to moderate student submissions to your course album? Select either radio button then click the "Enable moderation" button to save your setting.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_comment_disapproved', 'The following comment has been disapproved', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_note_comment_posted_new', 'The following comment is a new comment', NOW(), '');


INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_admin_index', 'Administer a photo album', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_index', 'Thumbnail view', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_view', 'View picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_file_upload', 'Upload a picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_delete_image', 'Delete a picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_add_image', 'Add a picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_save_image_order', 'Save image order', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_edit_image', 'Edit a picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_delete_comment', 'Delete comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_add_comment', 'Add comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_edit_comment', 'Edit comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_image_info_add', 'Add picture information', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_comment_add', 'Add comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_comment_edit', 'Edit comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_image_info_edit', 'Edit picture information', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_photo_new', 'New pictures (My pictures)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_photo_approved', 'Approved pictures (My pictures)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_photo_disapproved', 'Disapproved pictures (My pictures)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_photo', 'Photo Album - My Photos', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_comment_new', 'New comments (My comments)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_comment_approved', 'Approved comments (My comments)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_comment_disapproved', 'Disapproved comments (My comments)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_my_comment', 'Photo Album - My Comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_new_pic', 'New pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_approved_pic', 'Approved pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_disapproved_pic', 'Disapproved pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_new_comment', 'New comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_approved_comment', 'Approved comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_disapproved_comment', 'Disapproved comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_approved_pic', 'Approved pictures (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_disapproved_pic', 'Disapproved pictures (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_new_pic', 'New pictures (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_new_comment', 'New comments (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_approved_comment', 'Approved comments (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_disapproved_comment', 'Disapproved comments (Instructor view)', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_image', 'Photo album', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_comment', 'Manage comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_instructor_config', 'Enable moderation', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_comment', 'Manage comments', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_title_administrator_config', 'Enable Moderation', NOW(), '');



INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_new_pics', 'New pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_approved_pics', 'Approved pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_disapproved_pics', 'Disapproved pictures', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_new_pic', 'Set picture as new', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_approved_pic', 'Set picture as approved', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_disapproved_pic', 'Set picture as disapproved', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_new_comment', 'Set comment as new', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_approved_comment', 'Set comment as approved', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_set_disapproved_comment', 'Set comment as disapproved', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_add_image', 'Add a new picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_edit_image', 'Edit this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_del_image', 'Delete this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_add_comment', 'Add a new comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_edit_comment', 'Edit this comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_del_comment', 'Delete this comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_goback_thumbnail', 'Back to the photo album thumbnail view', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_upload_image', 'Upload this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_update_image', 'Update this picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_upload_image_info', 'Upload picture information', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_update_image_info', 'Update picture information', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_upload_comment', 'Add this comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_update_comment', 'Update this comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_skip_upload_image', 'Skip this step', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'pa_button_config_change', 'Enable moderation', NOW(), '');



INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_ADD_IMAGE_SUCCESS_CONFIG_ENABLED', 'Your picture has been added successfully.  It will be displayed in the course photo album if the instructor approves the picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_ADD_IMAGE_SUCCESS_CONFIG_DISABLED', 'Your picture has been added successfully.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_ADD_COMMENT_SUCCESS_CONFIG_ENABLED', 'Your comment has been added successfully.  It will be displayed if the instructor approves the comment', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_ADD_COMMENT_SUCCESS_CONFIG_DISABLE', 'Your comment has been added successfully', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_EDIT_IMAGE_SUCCESS_CONFIG_ENABLED', 'Your picture has been edited successfully.  It will be displayed if the instructor approves the picture.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_EDIT_IMAGE_SUCCESS_CONFIG_DISABLED', 'Your picture has been edited successfully.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_EDIT_COMMENT_SUCCESS_CONFIG_ENABLED', 'Your edit has been made successfully.  Your comment will be displayed if the instructor approves it.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_EDIT_COMMENT_SUCCESS_CONFIG_DISABLED', 'Your edit has been made successfully', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_DELETE_IMAGE_SUCCESS', 'Your picture was deleted successfully', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_PA_DELETE_COMMENT_SUCCESS', 'Your comment was deleted successfully', NOW(), '');


INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_FILE_TYPE', 'Your picture is not a valid picture file. Only jpeg, bmp, gif or png  picture files can be shared. Please try uploading a different picture or try converting your picture into a suitable format', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_FILE_SIZE', 'Your picture is larger than the maximum file size allowed by the web site. Please choose a smaller picture', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_FILE_EMPTY', 'You must find a picture to upload. Please use the "Browse" button to find a picture to upload', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_FILE_LENGTH', 'Your picture file name is longer than the maximum length allowed by the web site. Please rename your picture then try again', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_FILE_ESCAPE', 'The name of your picture file contains characters not allowed by the web site. Please rename your picture then try again', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_TITLE_EMPTY', 'Please give your picture a name by filling in the title field', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_COMMENT_EMPTY', 'To leave a comment about this picture, please fill in the comment form', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_ALT_EMPTY', 'Please provide a description of the picture for the image alt attribute', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_IMAGE_NOT_ALLOWED', 'You are not allowed to modify this picture. You can only make changes to pictures submitted by you', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_USER_COMMENT_NOT_ALLOWED', 'You are not allowed to modify this comment. You can only change comments left by you', NOW(), '');

INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_VAR_UNAUTHORIZED', 'An error has occurred. A page was requested in an unexpected way. Please try to complete your task in a different way.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_VAR_COURSE_EMPTY', 'There are no courses on this server. Please create a course first before trying to administer the photo album.', NOW(), '');

INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_PA_INDEX', 'An error has occurred in the Pa_Index() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_VIEW', 'An error has occurred in the View() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_PA_ADMIN_COMMENT', 'An error has occurred in the Pa_Admin_Comment() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_PA_ADMIN_IMAGE', 'An error has occurred in the Pa_Admin_Image() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_MYPIC', 'An error has occurred in the Mypic() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_PA_ADMIN_IMAGE', 'An error has occurred in the Pa_Admin_Image() object.\n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_OBJ_IMAGE_UPLOAD', 'An error has occurred in the Image_Upload() object.\n Please report this error to your technical support person', NOW(), '');


INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_MAKE_TEMP_FOLDER', 'The web site could not create or write to the images folder. \n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_DELETE_IMAGE', 'The picture could not be deleted from the database. \n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_DELETE_BLOG', 'The comment could not be deleted from the database. \n Please report this error to your technical support person', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_MKDIR', 'The web site photo album module could not create the image folder for you.  Please ask technical support to check that the album_image folder exists and that it is writable', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_COPY', 'The web site photo album module could not copy the picture to the destination folder.  Please ask technical support to check that the album_image folder exists and that it is writable', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_STORE_IMAGE_IN_DATABASE', 'The web site photo album module could not store the picture information in the database.  Please ask technical support to check that the database server is running properly', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_UPDATE_IMAGE_IN_DATABASE', 'The web site photo album module could not update the image information in the database.  Please ask technical support to check that the database server is running properly', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_STORE_COMMENT_IN_DATABASE', 'The web site photo album module could not store the comment information in the database.  Please ask technical support to check that the database server is running properly', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_UPDATE_COMMENT_IN_DATABASE', 'The web site photo album module could not update the comment information in the database.  Please ask technical support to check that the database server is running properly', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_UNLINK', 'The web site photo album module could not delete the temporary picture file.  Please ask technical support to check that the album_image folder exists and that it is writable. ', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_CLEAR_TEMP_FOLDER_OPEN', 'The web site photo album module could not open the temp folder.  Please ask technical support to check that the temp folder exists and that it is writable.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PA_FUNC_CLEAR_TEMP_FOLDER_UNLINK', 'The web site photo album module could not delete temp files from the temp folder.  Please ask technical support to check that the temp folder exists and that it is writable.', NOW(), '');