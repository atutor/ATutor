# sql file for calendar module

CREATE TABLE `calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256),
  `start` datetime,
  `end` datetime,
  `allDay` varchar(20),
  `userid` int(8),
  PRIMARY KEY (`id`)
);

CREATE TABLE `calendar_google_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(256),
  `userid` int(8),
  `calids` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `calendar_bookmark` (
  `memberid` int(11),
  `ownerid` int(8),
  `courseid` int(8),
  `calname` varchar(256)
);

CREATE TABLE `calendar_notification` (
  `memberid` int(11),
  `status` int(8)
);

# language text

INSERT INTO `language_text` VALUES ('en', '_module','calendar','Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_header','Calendar',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_import_file','Import ics file',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_upload_file','Upload ics file',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_submit','Submit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_export_file','Export ics file',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_options','Calendar Options',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_disconnect_gcal','Disconnect from Google Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_connect_gcal','Connect with Google Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_gcals','Google Calendars',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_internal_events','ATutor Internal Events',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_persnl','Personal Events',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_assign_due','Assignment Due Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_assign_cut','Assignment Cut off Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_uneditable','Uneditable event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_course_rel','Course Release Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_course_end','Course End Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_test_start','Test Start Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_events_test_end','Test End Date',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_tooltip_cell','Click or press enter to create event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_tooltip_event','Click or press enter to edit event',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_title','Event Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_start_d','Start Date (yyyy-mm-dd)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_start_t','Start Time (24hours)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_end_d','End Date (yyyy-mm-dd)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_end_t','End Time (24hours)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_form_title_def','Event Name',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_next','Next',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_prev','Previous',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_today','Today',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_month','Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_week','Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_day','Day',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_nxt_mnth','Next Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_prv_mnth','Previous Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_nxt_week','Next Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_prv_week','Previous Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_nxt_day','Next Day',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_prv_day','Previous Day',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_creat_e','Create Event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_cancel_e','Cancel',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_edit_e','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_del_e','Delete Event',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_share','Share Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_bookmarkd','Bookmarked Calendars',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_of','Calendar of',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_viewcal','View',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_sendall','Send to all other students in this course',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_sellist','Select from list',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_manemail','Enter email address',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_mailtxt','Enter email',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_membrselect','Select member',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_titletxt','Title of Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_bookmark_this','Bookmark this Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_del_bookmark','Remove Bookmark',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_save','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_edit_title','Edit Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_view_title','View Shared Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_mail_title','Shared Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_optional_fld','Optional: If the title is not specified, default title will be set to "Calendar of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_no_one_else','You are the only one in this course, no available recipients.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_public_note1','You can',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_public_note2','login',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_public_note3','to bookmark this calendar.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_pub_def_msg','Use this tab to view shared calendar.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_error','Error: ',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_test_start','Start date of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_test_end','End date of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_test_token',' test',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_course_start','Release date of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_course_end','End date of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_course_token',' course',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_assignment_due','Due date of ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_assignment_cut','Cut off date of ',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','calendar_patch_error','Install Patch First.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_email_part1',' has shared ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_email_part2',' with you. You may browse calendar at: 
',NOW(),'');

#email notifications

INSERT INTO `language_text` VALUES ('en', '_module','calendar_notification','Email Notifications',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_on','On',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_off','Off',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_turn','Turn',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_title','ATutor Calendar Notification',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_mail_1','Events for tomorrow: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_mail_2','Event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_mail_3','Start',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_mail_4','End',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','calendar_noti_mail_5','Event',NOW(),'');

# feedback messages

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_INVALID_EMAIL','Email address is invalid.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_ALREADY_BOOKMARKED','Calendar is already bookmarked.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CAL_FILE_ERROR','Error in file processing.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CAL_FILE_DELETE','Error in removing duplicate file.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_NO_RECIPIENTS','There are no recipients of this email.',NOW(),'');