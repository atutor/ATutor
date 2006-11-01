###############################################################
# Database upgrade SQL from ATutor 1.5.3.2 to ATutor 1.5.3.3
###############################################################

# convert DATETIME fields to TIMESTAMP
ALTER TABLE `admins` CHANGE `last_login` `last_login` TIMESTAMP NOT NULL;
ALTER TABLE `admin_log` CHANGE `time` `time` TIMESTAMP NOT NULL ;

ALTER TABLE `backups` CHANGE `date` `date` TIMESTAMP NOT NULL ;
ALTER TABLE `blog_posts` CHANGE `date` `date` TIMESTAMP NOT NULL ;
ALTER TABLE `blog_posts_comments` CHANGE `date` `date` TIMESTAMP NOT NULL ;
ALTER TABLE `content` CHANGE `last_modified` `last_modified` TIMESTAMP NOT NULL ;

ALTER TABLE `faq_entries` CHANGE `revised_date` `revised_date` TIMESTAMP NOT NULL ;
ALTER TABLE `files` CHANGE `date` `date` TIMESTAMP NOT NULL ;
