# sql file for gradebook module

CREATE TABLE `grade_scales` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `scale_name` VARCHAR(255) NOT NULL ,
   `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
   PRIMARY KEY ( `grade_scale_id` )
);

CREATE TABLE `grade_scales_detail` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL,
   `scale_value` VARCHAR(50) NOT NULL ,
   `percentage_from` MEDIUMINT NOT NULL default '0',
   `percentage_to` MEDIUMINT NOT NULL default '0'
);

CREATE TABLE `gradebook_tests` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `test_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or tests.test_id. 0 for external tests. tests.test_id for ATutor tests',
   `course_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or courses.course_id. 0 for ATutor tests, test_id can be retrieved from table "tests". courses.course_id for external tests.',
   `title` VARCHAR(255) NOT NULL COMMENT 'Values: Null or test name. Null for ATutor tests, test name can be retrieved from "tests.title". Not null value for external tests.',
   `due_date` date NOT NULL default '0000-00-00',
   `grade_scale_id` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY ( `gradebook_test_id` )
);

CREATE TABLE `gradebook_detail` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `grade` VARCHAR(255) NOT NULL,
   UNIQUE INDEX (`gradebook_test_id`, `member_id`)
);

INSERT INTO `grade_scales` (member_id, scale_name, created_date) values (0, 'Letter Grade', now());
INSERT INTO `grade_scales` (member_id, scale_name, created_date) values (0, 'Competency 1', now());
INSERT INTO `grade_scales` (member_id, scale_name, created_date) values (0, 'Competency 2', now());

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A+', 90, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A', 80, 89);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'B', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'C', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'D', 50, 59);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'E', 0, 49);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Pass', 75, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Fail', 0, 74);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Excellent', 80, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Good', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Adequate', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Inadequate', 0, 59);


INSERT INTO `language_text` VALUES ('en', '_module','gradebook','Gradebook',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','grade_scale','Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_grade_scale','Add Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','edit_grade_scale','Edit Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','delete_grade_scale','Delete Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','custom_grade_scale','Custom Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','preset_grade_scale','Preset Grade Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','custom','Custom',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','scale','Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','scale_value','Scale Value',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','percentage_from','Percentage From',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','percentage_to','Percentage To',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','scale','Scale',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','grade','Grade',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','edit_marks','Edit Marks',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_tests','Add Tests/Assignments',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','edit_tests','Edit Tests/Assignments',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','is_atutor_test','Is ATutor Test',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_atutor_test','Add ATutor Test',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_external_test','Add External Test',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','all_atutor_tests','All Applicable ATutor Tests',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_atutor_test_info','Note: In order to use gradebook, test property "Attempts Allowed" can only be 1. If "Grade Scale" is set to none, raw final score is to be used in gradebook.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','raw_final_score','Raw Final Score',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','update_gradebook','Update ATutor Marks into Gradebook',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','import_export_external_marks','Import/Export External Marks',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','export_marks_info','To simplify the import process, you can export an empty csv file on the test you want to import, fill in the marks, import back into ATutor.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','import_marks_info','A mark list may be imported into ATutor. Create the mark list in a comma separated values (CSV) format as follows: "firstname", "lastname", "email", "mark" with one student per line. Please leave the first line as title. The mark in CSV file can be grade or percentage like 50%%. To simplify the process, you can export the CSV file with export functionality, update the marks into exported file and import back into ATutor.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','student_not_exists','Student not exists',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','empty_gradebook','Gradebook is empty.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','completed_date','Completed Date',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','your_mark','Your Mark',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','class_avg','Class Avg',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','completed','Completed',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_GRADE_SCALE','Are you sure you want to <strong>delete</strong> grade scale <strong>%s</strong>?',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_TEST_FROM_GRADEBOOK','Are you sure you want to <strong>delete</strong> test <strong>%s</strong> from gradebook?',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_ADD_TEST_INTO_GRADEBOOK', '"<strong>%1$s</strong>" cannot be added into gradebook because the following students have taken it more than once:<br />\r\n%2$s.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_UPDATE_GRADEBOOK', '"<strong>%1$s</strong>" cannot be updated into gradebook because the following students have taken it more than once:<br />\r\n%2$s.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_GRADEBOOK_UPDATED', 'The following grades have been successfully updated into gradebook: <ul> %s </ul>',NOW(),'gradebook');
