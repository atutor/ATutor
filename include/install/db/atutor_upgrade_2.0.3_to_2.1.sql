# -------------- Multi-sites Support Starts -----------------

# CREATE TABLE `subsites` (
# 	`site_id` mediumint(10) NOT NULL AUTO_INCREMENT,
# 	`site_name` mediumint(10) NOT NULL DEFAULT '0',
# 	`site_URL` varchar(255) NOT NULL,
# 	`site_type` varchar(10) NOT NULL ENUM('domain', 'directory'),
# 	`directory` varchar(255) NOT NULL, 
# 	`enabled` tinyint(1) NOT NULL DEFAULT '1',
# 	PRIMARY KEY ( `site_id` )
# ) ENGINE = MyISAM;

ALTER TABLE `themes` ADD customized tinyint(1) NOT NULL DEFAULT '1';

UPDATE `themes` SET customized = 0 WHERE dir_name in ('default', 'fluid', 'default_classic', 'blumin', 'greenmin', 'default15', 'default16', 'idi', 'mobile');

# -------------- Multi-sites Support Ends -----------------