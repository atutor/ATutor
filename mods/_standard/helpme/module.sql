####
#
# This file is obsolete, integrated into the atutor installer, and language database
#
# sql file for the HelpMe module

/*
CREATE TABLE IF NOT EXISTS `helpme_user` (
  `user_id` mediumint(8) NOT NULL,
  `help_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme','HelpMe',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_text','The HelpMe module presents a short series of prompts for new Administrators and Instructors  to help them quickly learn to use ATutor effectively. Click on checkbox below to enable or disable HelpMe. Use the HelpMe Language form below to modify the help messages presented to new users.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_disable','Enable/Disable HelpMe',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_dismiss','Next',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_dismiss_all','Dismiss All',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_language','HelpMe Language',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_message','Message',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_reset','See HelpMe messages again.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_module','helpme_revisit','Previous',NOW(),'');

#ADMIN
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CREATE_A_COURSE','You can <a href="%s">Create a Course</a> by opening the Courses tab above, then opening the Create Course tab in the sub-menu that appears.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_SYS_PREFS','You will likely want to adjust the <a href="%s">System Preferences</a> to match your requirements. Open the System Preferences tab above to modify ATutor settings.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_DEFAULT_TOOLS','You can adjust the <a href="%s">Default Tools</a> that are setup in a newly created course by opening the Courses tab above, then opening the Default Tools tab in the sub-menu that appears.' ,NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_ADMIN_CREATE_USER','The administrator may want to <a href="%s">Create Users</a>, or this can be left up to instructors using the Enrollment Manager. Or, students can register themselves if enabled in the System Preferences. Open the Users tab above, then open the Create User Account tab in the sub-menu.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_USERS_PREFS','The <a href="%s">Default Preferences</a> can be adjusted to control settings for new users.  Open the Users tab above, then open the Default Preferences tab in the sub-menu that appears.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CHANGE_THEME','The <a href="%s">Theme Manager</a> can be used to change the appearance of ATutor, choosing from several themes provided with the system, or by uploading custom created themes. Open the System Preferences tab above, then open the Themes tab in the sub-menu.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_MANAGE_MODULE','The <a href="%s">Module Manager</a> can be used manage the features installed and enabled on your ATutor system. Open the Modules Tab above to view the modules currently installed, and open the Add Module tab in the sub-menu to add new features, either importing from the main module repository, or uploading modules you have created yourself or downloaded from the Web.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_APPLY_PATCHES','It is important to keep your ATutor installation up to date using the <a href="%s">Patcher</a> to install bug fixes, security enhancements, and occassional feature adjustments. Open the Patcher tab above to review the patches installed on your system. If you are a developer, open the Create Patch tab in the sub-menu to create your own bug fixes or features that can be submitted and added to the public ATutor source code.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CREATE_ADMIN','It can be helpful to <a href="%s">Create Administrators</a> to perform specific tasks, assigning particular administrator tools to them to manage. Open the Users tab above, then open the Administrators tab in the sub-menu.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_READ_HANDBOOK','For more about using ATutor see the %s in the links at the bottom of the screen. Enter a keyword search to find information about any feature. Also notice the context sensitive ATutor Handbook tab that often appears alongside or above various tools for specific information about using that tool, and notice the <a href="%s">Help Page</a> in the upper right corner for community based help.',NOW(),'');

#Instructor
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CREATE_COURSE','If enabled, you can <a href="%s">Create a Course</a> by opening the My Courses tab on My Start Page. Click Create Course in the sub-menu tabs to start creating.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_MANAGE_ONOFF','While in a course as its instructor, notice the <span style="color:green;text-decoration:underline;">manage on</span>/<span style="color:red;text-decoration:underline;">manage off</span> toggle. Turn Manage On to add shortcut navigation to relevant course management tools.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CREATE_CONTENT','You can <a href="%s">Create Content</a> for your course that includes movies, images, slides, documents or text, among other formats. Under the Manage tab while in a course, open Create in the Content sub-menu to add new pages to your course. Also notice the Content Navigation block to the side, and the small toolbar there that can be used to quickly add folders or pages, and to edit the menu items below. Also notice the Editor Toolbar when viewing content, for quick access to all of content management tools. ',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_COURSE_TOOLS','You can select from a range of <a href="%s">Course Tools</a> to add particular features to a course. Tools can be added to the Main Tabs above, to the menu blocks at the side, or added as icons or boxes on the course home page. To manage the tools used in your course open the Manage tab while in a course, then open the Course Tools sub-menu.',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_ADD_USERS','After a course is setup and content added, the next step is often to <a href="%s">Create a Course List</a>, and enroll students. Students can be enrolled manually typing them in one at a time, by importing a list in a CSV text file, by adding students from those registered on the system, or allowing students to enroll themselves. To add students to your course open the Manage tab while in a course, then open the Enrollment sub-menu. ',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_COURSE_PROPERTIES','Modify the initial <a href="%s">Course Properties</a> that were set when the course was created. Open the Manage tab while in a course, then open the Properties sub-menu. ',NOW(),'');
REPLACE INTO `language_text_2_2_2` VALUES ('en', '_msgs','AT_HELP_CREATE_BACKUP','To protect your course <a href="%s">Create a Backup</a> that can be stored on your own computer, and restored in whole or in part whenever needed. Use backups to create a new session of a course, or to move a course to a different ATutor site. Also notice the tools for <a href="%s">packaging content and tests</a>, and others for exporting test data or archiving forums as additional ways to backup your course.',NOW(),'');
*/