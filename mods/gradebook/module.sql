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
   `percentage_to` MEDIUMINT NOT NULL default '0',
   PRIMARY KEY (`grade_scale_id`, `scale_value`)
);

CREATE TABLE `gradebook_tests` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0, tests.test_id or assignments.assignment_id. 0 for external tests/assignments. tests.test_id for ATutor tests, assignments.assignment_id for ATutor assignments.',
   `type` VARCHAR(50) NOT NULL default '' COMMENT 'Values: ATutor Test, ATutor Assignment, External',
   `course_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or courses.course_id. Only has value for external tests/assignments. When ATutor internal assignments/tests/surveys, always 0.',
   `title` VARCHAR(255) NOT NULL COMMENT 'Values: Null or test name. Always null if ATutor internal assignments/tests/surveys.',
   `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
   `grade_scale_id` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY ( `gradebook_test_id` )
);

CREATE TABLE `gradebook_detail` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `grade` VARCHAR(255) NOT NULL,
   PRIMARY KEY (`gradebook_test_id`, `member_id`)
);

INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (1, 0, 'Letter Grade', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (2, 0, 'Competency 1', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (3, 0, 'Competency 2', now());

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
INSERT INTO `language_text` VALUES ('en', '_template','add_atutor_test','Add ATutor Assignments/Test/Surveys',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_external_test','Add External Assignments/Tests',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','all_atutor_tests','All Applicable ATutor Tests &amp; Surveys',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','all_atutor_assignments','All ATutor Assignments',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','add_atutor_test_info','Select from the available test and assignment titles, then optionally choose a  "Grade Scale", to add a test or assignment to the gradebook. . If "Grade Scale" is set to none, the raw final score will be used in place of a grade scale. Only tests with the test property "Attempts Allowed" set to 1 can be added to the Gradebook. Create tests using the ATutor Tests & Surveys Manager',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','raw_final_score','Raw Final Score',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','update_gradebook','Update ATutor Marks',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','import_export_external_marks','External Marks',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','export_marks_info','To simplify the import process, you can export an empty csv file on the test you want to import, fill in the marks, import back into ATutor.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','import_marks_info','A mark list may be imported into ATutor. Create the mark list in a comma separated values (CSV) format as follows: "firstname", "lastname", "email", "mark" with one student per line. Please leave the first line as title. The mark in CSV file can be grade or percentage like 50%%. To simplify the process, you can export the CSV file with export functionality, update the marks into exported file and import back into ATutor.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','student_not_exists','Student not exists',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','empty_gradebook','Gradebook is empty.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','completed_date','Completed Date',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','your_mark','Your Mark',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','class_avg','Class Avg',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','completed','Completed',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','external_tests','External Tests',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','grade_already_exists','Conflict: Grade already exists - %s',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','how_to_solve_conflict','How to solve conflict',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','use_higher_grade','Use higher grade',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','use_lower_grade','Use lower grade',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','not_overwrite','Not overwrite',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','grades_uncomparable','Grades are uncomparable. Choose another way to solve conflict',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','combine_tests','Combine ATutor Tests',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','combine','Combine',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','combine_into','Combine Into',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','combine_from','Combine From',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','combine_tests_info','Before combining tests, please run section above to update marks of "Combine Into Test/Survey."',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_template','grade_info','Note: "Grade" field can be grade defined in "Grade Scale" or percentage like 50%%.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_GRADE_SCALE','Are you sure you want to <strong>delete</strong> grade scale <strong>%s</strong>?',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_TEST_FROM_GRADEBOOK','Are you sure you want to <strong>delete</strong> test <strong>%s</strong> from gradebook?',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_ADD_TEST_INTO_GRADEBOOK', '"<strong>%1$s</strong>" cannot be added into gradebook because the following students have taken it more than once:<br />\r\n%2$s.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_COMBINE_TESTS', '"<strong>%1$s</strong>" cannot be combined because the following students have taken it more than once:<br />\r\n%2$s.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_UPDATE_GRADEBOOK', '"<strong>%1$s</strong>" cannot be updated into gradebook because the following students have taken it more than once:<br />\r\n%2$s.',NOW(),'gradebook');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_FEEDBACK_GRADEBOOK_UPDATED', 'The following grades have been successfully updated into gradebook: <ul> %s </ul>',NOW(),'gradebook');
