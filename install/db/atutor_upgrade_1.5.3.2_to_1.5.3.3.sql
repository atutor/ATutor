###############################################################
# Database upgrade SQL from ATutor 1.5.3.2 to ATutor 1.5.3.3
###############################################################

# convert DATETIME fields to TIMESTAMP
ALTER TABLE `admins`                CHANGE `last_login` `last_login`       TIMESTAMP     NULL ;
ALTER TABLE `admin_log`             CHANGE `time` `time`                   TIMESTAMP NOT NULL ;

ALTER TABLE `backups`               CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `blog_posts`            CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `blog_posts_comments`   CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `content`               CHANGE `last_modified` `last_modified` TIMESTAMP NOT NULL ;

ALTER TABLE `faq_entries`           CHANGE `revised_date` `revised_date`   TIMESTAMP NOT NULL ;
ALTER TABLE `files`                 CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `files_comments`        CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `forums`                CHANGE `last_post` `last_post`         TIMESTAMP NOT NULL ;
ALTER TABLE `forums_threads`	    CHANGE `last_comment` `last_comment`   TIMESTAMP NOT NULL ,
                                    CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `handbook_notes`        CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `instructor_approvals`  CHANGE `request_date` `request_date`   TIMESTAMP NOT NULL ;
ALTER TABLE `members`               CHANGE `creation_date` `creation_date` TIMESTAMP NOT NULL ;
ALTER TABLE `member_track`          CHANGE `last_accessed` `last_accessed` TIMESTAMP     NULL ;
ALTER TABLE `messages`              CHANGE `date_sent` `date_sent`         TIMESTAMP NOT NULL ;
ALTER TABLE `news`                  CHANGE `date` `date`                   TIMESTAMP NOT NULL ;
ALTER TABLE `polls`                 CHANGE `created_date` `created_date`   TIMESTAMP NOT NULL ;
ALTER TABLE `tests_results`         CHANGE `date_taken` `date_taken`       TIMESTAMP NOT NULL ;
