# sql file for hello world module

CREATE TABLE `wordpress` (
   `wordpress_id` mediumint(8) unsigned NOT NULL,
   `course_id` mediumint(8) unsigned NOT NULL,
   `value` VARCHAR( 30 ) NOT NULL ,
   PRIMARY KEY ( `wordpress_id` )
) ENGINE = MyISAM;

INSERT INTO `language_text` VALUES ('en', '_module','wordpress','WordPress',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_admin_login','Login to Administer WordPress',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_base_url','WordPress base URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_login','WordPress Login',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_host_url','WordPress Host URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_db_name','WordPress Database Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_db_port','WordPress Database Port',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_db_user','WordPress Database User',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_db_pwd','WordPress Database Password',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_db_prefix','WordPress Database Table Prefix',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_save','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_no_iframes','Your browser does not support iframes. Go to <a href="'.%s.'">WordPress Login</a>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_do_setup','Enter the URL to the WordPress based directory in the form field above, including a trailing slash (/), to have WordPress appear here.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','wordpress_no_db_info','To access current information here, modify the wp_config.php file in the WordPress module.',NOW(),'');




INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_WP_CONFIG_SAVED','Wordpress configuration successfully saved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_WP_CONFIG_FAIL','Wordpress configuration failed to save. ',NOW(),'');