###############################################################
# Database upgrade SQL from ATutor 1.6 to ATutor 1.6.1
###############################################################

# support new changes for Test/Survey
INSERT INTO `language_text` VALUES 
('en', '_template', 'pass_score', 'Pass Score', now(), ''),
('en', '_template', 'pass_feedback', 'Pass Feedback', now(), ''),
('en', '_template', 'fail_feedback', 'Fail Feedback', now(), ''),
('en', '_template', 'test_description', 'Test Description', now(), ''),
('en', '_template', 'no_pass_score', 'No pass score', now(), ''),
('en', '_template', 'percentage_score', 'percentage score', now(), ''),
('en', '_template', 'points_score', 'points score', now(), ''),
('en', '_template', 'all_passed_students', 'All Passed Students', now(), ''),
('en', '_template', 'all_failed_students', 'All Failed Students', now(), '')
;

ALTER TABLE `tests`
ADD `description` TEXT NOT NULL AFTER `title` , 
ADD `passscore` MEDIUMINT NOT NULL AFTER `content_id` , 
ADD `passpercent` MEDIUMINT NOT NULL AFTER `passscore` ,
ADD `passfeedback` TEXT NOT NULL AFTER `passpercent` , 
ADD `failfeedback` TEXT NOT NULL AFTER `passfeedback` ;

