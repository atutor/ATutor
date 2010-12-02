# set the type of the mobile theme
UPDATE `themes` SET `type` = 'Mobile' WHERE `dir_name` = 'mobile';

# add the Vimeo module 
INSERT INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) VALUES('_standard/vimeo', 2, 0, 1, 0, 0);

# add the IDI Default theme
INSERT INTO `themes` VALUES ('IDI Theme', '2.0', 'idi', 'Desktop', '2010-12-02', 'The theme created for the IDI course server.', '1');