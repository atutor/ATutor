###############################################################
# Database upgrade SQL from ATutor 1.5.4 to ATutor 1.5.5
###############################################################


## remove login field - #3032
ALTER TABLE `forums_threads` DROP `login`;

## refresh test issue - #2362
ALTER TABLE `tests_questions_assoc` DROP INDEX `test_id`;
ALTER TABLE `tests_results` ADD `status` TINYINT NOT NULL DEFAULT '0';
UPDATE TABLE `tests_results` SET status=1, date_taken=date_taken;

## times tests - #3084
ALTER TABLE `tests_results` ADD `end_time` TIMESTAMP NOT NULL ;
UPDATE TABLE `tests_results` SET date_taken=date_taken, end_time=date_taken;

## end date - #3089
ALTER TABLE `courses` ADD `end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `release_date`;
