#
# sql file for announcement_subscription module
#


# Set up table for subscribers
CREATE TABLE `courses_members_subscription` (
  `member_id` MEDIUMINT NOT NULL ,
  `course_id` MEDIUMINT NOT NULL ,
  `subscribe` MEDIUMINT NULL DEFAULT '0',
  PRIMARY KEY (member_id)
) TYPE=MyISAM;



# Insert module specific language:
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_subscription',
                                    'Announcement Subscription',
                                     NOW(),
                                    'Title');
                                    
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_subscribe_subject',
                                    'Course Announcement Subscription',
                                     NOW(),
                                    'Announcement subscription mail subject');                           
                                    
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_subscribe_body',
                                    '*DO NOT REPLY TO THIS MESSAGE* \n
You are now subscribed to the newsfeed in the ATutor course "%s". Login at: %s to view the course.',
                                     NOW(),
                                    'New announcement mail body');
                                    
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_unsubscribe_body',
                                    '*DO NOT REPLY TO THIS MESSAGE* \n
Your subscription to the newsfeed in the ATutor course "%s" has been cancelled. Login at: %s to view the course.',
                                     NOW(),
                                    'New announcement mail body');   
                                    
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_notify_subject',
                                    'New Course Announcement',
                                     NOW(),
                                    'New announcement mail subject');
                                    
INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_notify_body1',
                                    '*DO NOT REPLY TO THIS MESSAGE* \n
A new announcement has been published in the ATutor course "%s". Login at: %s to view the course.',
                                     NOW(),
                                    'New announcement mail body');

INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_subscription_subscribe',
                                    'Subscribe',
                                     NOW(),
                                    'Subscribe');

INSERT INTO `language_text` VALUES ('en',
                                    '_module',
                                    'announcement_subscription_unsubscribe',
                                    'Unsubscribe',
                                     NOW(),
                                    'Unsubscribe'); 

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_INFOS_ANNOUNCEMENTSUB_ALREADYINSTALLED_ADDNEWS',
                                    'Module already installed in file /editor/add_news.php ',
                                     NOW(),
                                    'Module installed warning');                                  

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_FEEDBACK_ANNOUNCEMENTSUB_INSTALL_ADDNEWS',
                                    'Changes made to file /editor/add_news.php ',
                                     NOW(),
                                    'Module installed feedback');

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_ERROR_ANNOUNCEMENTSUB_INSTALL_ADDNEWS',
                                    'Could not write to file /editor/add_news.php ',
                                     NOW(),
                                    'Module install error');

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_FEEDBACK_ANNOUNCEMENTSUB_SUBSCRIBE',
                                    'You have successfully subscribed to the announcement newsfeed for this course.',
                                     NOW(),
                                    'Subscribed feedback message');   

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_ERROR_ANOUNCEMENTSUB_INSTALL_UNWRITE',
                                    'Cannot write to file /editor/add_news.php. Please make sure appropriate permissions are set for writing to this file.',
                                     NOW(),
                                    'file unwritable error');                                          

INSERT INTO `language_text` VALUES ('en',
                                    '_msgs',
                                    'AT_FEEDBACK_ANNOUNCEMENTSUB_UNSUBSCRIBE',
                                    'You have successfully unsubscribed from the announcement newsfeed for this course.',
                                     NOW(),
                                    'Unsubscribed feedback message');          
