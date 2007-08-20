###############################################################
# Database upgrade SQL from ATutor 1.5.5 to ATutor 1.5.6
###############################################################

# one question per page - #3090
ALTER TABLE `tests_results` ADD `max_pos` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tests` ADD `display` TINYINT NOT NULL DEFAULT '0';
