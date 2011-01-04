###############################################################
# Database upgrade SQL from ATutor 1.5.3.1 to ATutor 1.5.3.2
###############################################################

UPDATE `modules` SET `cron_interval` = '1440' WHERE `dir_name` = '_core/languages';
