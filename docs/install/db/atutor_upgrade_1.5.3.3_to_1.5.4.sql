###############################################################
# Database upgrade SQL from ATutor 1.5.3.3 to ATutor 1.5.4
###############################################################

## alter the test questions table to support matching type questions

ALTER TABLE `tests_questions` ADD `option_0` VARCHAR( 255 ) NOT NULL AFTER `answer_9` ,
ADD `option_1` VARCHAR( 255 ) NOT NULL AFTER `option_0` ,
ADD `option_2` VARCHAR( 255 ) NOT NULL AFTER `option_1` ,
ADD `option_3` VARCHAR( 255 ) NOT NULL AFTER `option_2` ,
ADD `option_4` VARCHAR( 255 ) NOT NULL AFTER `option_3` ,
ADD `option_5` VARCHAR( 255 ) NOT NULL AFTER `option_4` ,
ADD `option_6` VARCHAR( 255 ) NOT NULL AFTER `option_5` ,
ADD `option_7` VARCHAR( 255 ) NOT NULL AFTER `option_6` ,
ADD `option_8` VARCHAR( 255 ) NOT NULL AFTER `option_7` ,
ADD `option_9` VARCHAR( 255 ) NOT NULL AFTER `option_8` ;

## alter the tests table to support guest tests
ALTER TABLE `tests` ADD `guests` TINYINT NOT NULL DEFAULT '0';