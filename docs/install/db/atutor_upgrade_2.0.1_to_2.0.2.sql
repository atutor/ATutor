# set the type of the mobile theme
UPDATE `themes` SET `type` = 'Mobile' WHERE `dir_name` = 'mobile';

# add the Vimeo module 
INSERT INTO `AT_modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) VALUES('_standard/vimeo', 2, 0, 1, 0, 0);
