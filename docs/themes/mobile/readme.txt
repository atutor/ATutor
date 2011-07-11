

Theme:		1.6.4 Mobile Theme
Date:		December 2009


Installing:	 See section "Installing a New Theme" in the themes_readme.txt file located in the themes/ top directory.

Licence:	Falls under the GPL agreement.  See http://www.gnu.org/copyleft/gpl.html.
	
	
GLOBAL TASKS & BUGS (for all devices)----------------------------------------------------------------

- rm content directory and config.inc.php file in SVN *** DONE
- The main admin modules screen is missing Type, Cron, and Directory Name columns *** DONE
- The admin's Translate screen, the translate button is enabled when translation is turned off in vitals.inc.php It should be greyed out, so maybe a style or bit of js is missing from the template.
- On the admin's home page in the Instructor requests box reads "array"  *** DONE
- RE-CHECK DEFAULT/USERS/DEFAULT_PREFERENCES.TMPL.PHP
- A11Y TASK: research whether to add ARIA landmarks (e.g. Main, Navigation) to header.tmpl.php & footer.tmpl.php (August)
- A11Y TASKhtml-check the Instructor mobile pages. (August)
- MOBILE FSS BUG: Create a workaround for Mobile FSS bug  ***In Process
    See: http://issues.fluidproject.org/browse/FLUID-4313
    - white list arrow should change color >
    - top navigation bar "home" & "help" buttons should highlight. 
    
- Instructor user: Content menu open/close stays highlighted after close - touch highlighting. 
- Instructor user: remove on keydowns (only present on index pages) 
- Instructor user: Improve styling: /docs/mods/_standard/statistics/course_stats.php - remove inline styles, fix markup
- Admin user: Improve styling: /docs/mods/_core/courses/admin/default_mods.php 
- Admin user: Improve styling: http://localhost/GSoC2011/docs/admin/error_logging.php
- Is there a way to handle long names so they don't break the styling:  e.g.: /docs/admin/cron_config.php 
- Done ? Create course / Edit course / Course properties for admin / instructor users. 
- default student leader: preferences. 
- Take commented-out visited link code and erase from android.css and iphone.css
- Student user: properties page isn't themed. Why? 
- template groups/ social networking (e.g. 
/mods/_standard/social/index_public.php)


STUDENT VIEW TABLET TASKS / BUGS / INFO ----------------------------------------------------------------
- implement device detection ***DONE
- create list of pages that need templating. 


iPHONE TASKS / BUGS / INFO ----------------------------------------------------------------
- A11Y BUG: Voice over bug:  admin user logs out in voice over when using the drop-down menu. 
	Only affects instructor users. Why? 
- A11Y TASK: do manual check for refine results open/close, i.e. make sure it can be used by Voice Over. 
Fix styling of "refine results" link after it has been opened/closed.  Sync styling changes with Android. 
	Affects: 	
	---	/docs/mods/_core/users/users.php 
	--- /docs/mods/_core/users/master_list.php 
	---	/docs/mods/_core/courses/admin/courses.php 
	--- /docs/mods/_core/modules/index.php 

ANDROID TASKS / BUGS / INFO ----------------------------------------------------------------
localhost: 10.0.2.2.
local Android emulator: /Users/alisonbenjamin/Documents/ATutor\ Design/android-sdk-mac_86/tools/android

BLACKBERRY TASKS / BUGS / INFO ----------------------------------------------------------------
http://stackoverflow.com/questions/61449/how-do-i-access-the-host-from-vmware-fusion


DEFAULT THEME a11y REVIEW NEEDED: (fieldset/legends and onkeydown might be added).---------------------------------
/docs/mods/_core/users/users.php 
/docs/mods/_core/users/instructor_requests.php
/docs/mods/_core/users/master_list.php 
/docs/mods/_core/users/admins/index.php 
/docs/mods/_core/users/admins/log.php 
/docs/mods/_core/courses/admin/courses.php 
/docs/mods/_standard/forums/admin/forums.php 
/docs/mods/_core/courses/admin/default_mods.php 
/docs/mods/_core/modules/index.php 
/docs/mods/_standard/rss_feeds/index.php 
/docs/mods/_standard/announcements/index.php
/docs/mods/_standard/assignments/index_instructor.php
/docs/mods/_core/backups/index.php
/docs/mods/_standard/chat/manage/index.php
/docs/mods/_core/content/index.php 
/docs/mods/_standard/tracker/tools/page_student_stats.php
/docs/mods/_standard/forums/index.php
/docs/mods/_standard/faq/index_instructor.php
/docs/mods/_standard/polls/tools/index.php
 
ADMINISTRATORS ----------------------------------------------------------------------------------

NOTE there are 3 errors in HTML validator due to using an ARIA role. 

[ADMIN-HOME] 
1. /docs/admin/index.php -  ****  DONE / WCAG AA / Valid HTML
2. /docs/mods/_core/users/admins/my_edit.php  ****  DONE / WCAG AA / Valid HTML
3. /docs/mods/_core/users/admins/my_password.php  ****  DONE / WCAG AA / Valid HTML

[USERS]
1.  /docs/mods/_core/users/user_enrollment.php **** DONE / WCAG AA / Valid HTML
2.  /docs/mods/_core/users/password_user.php **** DONE / WCAG AA / Valid HTML
3.  /docs/mods/_core/users/create_user.php **** DONE / WCAG AA / Valid HTML
4.  /docs/mods/_core/users/users.php **** DONE / WCAG AA / Valid HTML
5.  /docs/mods/_core/users/instructor_requests.php **** DONE / WCAG AA / Valid HTML
6.  /docs/mods/_core/users/master_list.php **** DONE / WCAG AA / Valid HTML (note: lacks fieldset, added onkeydown)
7.  /docs/mods/_core/users/admin_email.php **** DONE / WCAG AA / Valid HTML  
8.  /docs/mods/_core/users/admins/index.php **** DONE / WCAG AA / Valid HTML  (note: lacks fieldset, added onkeydown)
9.  /docs/mods/_core/users/admins/edit.php **** DONE / WCAG AA / Valid HTML  
10. /docs/mods/_core/users/admins/password.php **** DONE / WCAG AA / Valid HTML 
11. /docs/mods/_core/users/admins/create.php **** DONE / WCAG AA / Valid HTML
12. /docs/mods/_core/users/admins/log.php **** DONE / WCAG AA / Valid HTML
13. /docs/mods/_core/users/admins/reset_log.php  **** DONE / WCAG AA / Valid HTML  
14. /docs/mods/_core/users/edit_user.php?id=4 **** DONE / WCAG AA / Valid HTML

[COURSES]
1.  /docs/mods/_core/courses/admin/courses.php **** DONE / WCAG AA / Valid HTML (note: lacks fieldset, added onkeydown)
2.  /docs/mods/_core/properties/admin/edit_course.php *** DONE (Linearizes)
3.  /docs/mods/_standard/forums/admin/forums.php ****NOT DONE HTML (note: lacks fieldset, added onkeydown)
4.  /docs/mods/_standard/forums/admin/forum_add.php **** DONE / WCAG AA / Valid HTML
5.  /docs/mods/_standard/forums/admin/forum_edit.php **** DONE / WCAG AA / Valid HTML
6.  /docs/mods/_core/courses/admin/create_course.php *** DONE (Linearizes)
7.  /docs/mods/_core/enrolment/admin/index.php3 ****  DONE / WCAG AA / Valid HTML
8.  /docs/mods/_core/enrolment/admin/privileges.php  **** NOT DONE NEEDS TEMPLATING 
9.  /docs/mods/_core/courses/admin/default_mods.php *** DONE / WCAG AA / Valid HTML - should not be a part of mobile.
10. /docs/mods/_core/courses/admin/default_side.php **** DONE / HELP WCAG / Valid HTML
11. /docs/mods/_standard/support_tools/scaffolds.php **** DONE / WCAG AA / Valid HTML
12. /docs/mods/_core/cats_categories/admin/create_category.php  **** DONE /WCAG AA / Valid HTML
13. /docs/mods/_core/cats_categories/admin/course_categories.php **** NOT DONE NEEDS TEMPLATING  (subcategories must display)

[PATCHER] 
DON'T INCLUDE IN MOBILE THEME. 
http://localhost/GSoC2011/docs/mods/_standard/patcher/index_admin.php


[PHOTOS] 
1.  /docs/mods/_standard/photos/index_admin.php **** DONE / WCAG AA / Valid HTML 
2.  /docs/mods/_standard/photos/admin/preferences.php  **** DONE / WCAG AA / Valid HTML 

[MODULES] 
1.  /docs/mods/_core/modules/index.php **** DONE / WCAG AA / Valid HTML (note: lacks fieldset, added onkeydown) -  ?
2.  /docs/mods/_core/modules/install_modules.php  **** INSTALL MODULES SHOULD BE ENABLED FOR IPAD.CSS and NOT MOBILE -- TOO COMPLICATED 
3.  /docs/mods/_core/modules/details.php **** DONE / WCAG AA / Valid HTML *** won't text wrap. 

[SYSTEM PREFERENCES]
1.  /docs/admin/config_edit.php  **** DONE / WCAG AA / Valid HTML 
2.  /docs/mods/_core/languages/language_translate.php -- **** DONE, VALID WCAG, Valid HTML
3.  /docs/mods/_core/languages/language_import.php  -- **** DONE, WCAG AA, VALID HTML 
4.  /docs/mods/_core/languages/language_add.php - Channel bug, why is it blank? (also a problem in GSoC2011_TRUNK)
5.  /docs/mods/_core/languages/language_edit.php - Channel bug, why is it blank? (also a problem in GSoC2011_TRUNK)  
6.  /docs/mods/_core/languages/language.php -- **** DONE, VALID WCAG, HELP on HTML (CHANNEL BUG)  *** valid HTML except for fieldset
7.  /docs/mods/_core/languages/language_editor.php -  **** NOT DONE NEEDS TEMPLATING 
8.  /docs/mods/_standard/rss_feeds/preview.php
9.  /docs/mods/_standard/rss_feeds/edit_feed.php **** DONE / WCAG AA / Valid HTML
10.  /docs/mods/_standard/rss_feeds/index.php **** DONE / WCAG AA / Valid HTML (note: lacks fieldset, added onkeydown)
11. /docs/mods/_standard/rss_feeds/add_feed.php **** DONE / WCAG AA / Valid HTML
12. /docs/mods/_standard/tile_search/admin/module_setup.php **** DONE / HELP WCAG / HELP HTML (CHANNEL BUG)
13. /docs/mods/_standard/google_search/admin/module_prefs.php **** DONE, VALID WCAG, HTML good (except for legacy <b> tag)
14. /docs/mods/_standard/social/admin/delete_applications.php ==  already templated
15. /docs/mods/_standard/social/index_admin.php **** DONE / WCAG AA / Valid HTML
16. /docs/admin/cron_config.php **** DONE / WCAG AA / Valid HTML -- wrap bug. 
17. /docs/admin/error_logging.php

INSTRUCTORS ---------------------------------------------------------------------------------- 
	--- course home ***DONE 
	--- networking ***DONE 
	--- glossary ***DONE 
	--- mytracker ***DONE 
	--- index ***DONE

[INBOX]
/docs/inbox/index.php **** DONE
/docs/inbox/sent_messages.php **** DONE
/docs/inbox/send_message.php **** DONE
/docs/inbox/export.php **** DONE

[ANNOUNCEMENTS] 
/docs/mods/_standard/announcements/index.php **** DONE 
/docs/mods/_standard/announcements/add_news.php **** DONE
/docs/mods/_standard/announcements/edit_news.php **** DONE 

[ASSIGNMENTS]
/docs/mods/_standard/assignments/index_instructor.php **** DONE
NOT DONE: /docs/mods/_standard/assignments/add_assignment.php **** NOT DONE, remove for mobile? 

[BACKUPS]
/docs/mods/_core/backups/index.php  **** DONE 
/docs/mods/_core/backups/create.php **** DONE 
/docs/mods/_core/backups/edit.php **** DONE 
/docs/mods/_core/backups/upload.php **** DONE
/docs/mods/_core/backups/delete.php **** DONE 

[CHAT] 
/docs/mods/_standard/chat/manage/index.php **** DONE 
/docs/mods/_standard/chat/manage/start_transcript.php **** 

[CONTENT]
/docs/mods/_core/content/index.php  **** DONE 
http://localhost/GSoC2011/docs/mods/_core/editor/edit_content_folder.php?cid=240 **** DONE 
/docs/mods/_standard/tracker/tools/page_student_stats.php **** DONE 
/docs/mods/_standard/tracker/tools/index.php  **** DONE 
http://localhost/GSoC2011/docs/mods/_standard/tracker/tools/student_usage.php **** DONE 
http://localhost/GSoC2011/docs/mods/_standard/tracker/tools/reset.php ***** DONE 
/docs/mods/_core/editor/add_content.php **** NOT DONE, remove for mobile? 
http://localhost/GSoC2011/docs/mods/_core/editor/edit_content.php? **** NOT DONE, remove for mobile? (link from /index.php should be removed)

[COURSE EMAIL] 
http://localhost/GSoC2011/docs/mods/_standard/course_email/course_email.php **** DONE 

[COURSE TOOLS]
/docs/mods/_standard/course_tools/modules.php  ***  remove for mobile?
/docs/mods/_standard/course_tools/side_menu.php  ***  remove for mobile?

[ENROLLMENT]
/docs/mods/_core/enrolment/export_course_list.php **** DONE 
/docs/mods/_core/enrolment/import_course_list.php **** DONE 
/docs/mods/_core/enrolment/create_course_list.php *** Remove for mobile
/docs/mods/_core/enrolment/index.php **** DONE 
/docs/mods/_core/enrolment/privileges.php   **** NOT DONE

[FILE MANAGER]
NOT DONE: /docs/mods/_core/file_manager/index.php  ***  remove for mobile?

[FORUMS]
/docs/mods/_standard/forums/edit_forum.php  *** DONE
/docs/mods/_standard/forums/index.php  *** DONE
/docs/mods/_standard/forums/add_forum.php  *** DONE
/docs/mods/_standard/farchive/index_instructor.php  *** DONE


[FAQ] 
/docs/mods/_standard/faq/add_question.php  *** DONE
/docs/mods/_standard/faq/index_instructor.php  *** DONE
/docs/mods/_standard/faq/add_topic.php *** DONE  *** DONE
/docs/mods/_standard/faq/edit_topic.php *** DONE  *** DONE
/docs/mods/_standard/faq/edit_question.php  *** DONE


[GLOSSARY]
/docs/mods/_core/glossary/tools/add.php
NOT DONE

[GRADEBOOK]
NOT DONE

[GROUPS]
/docs/mods/_core/groups/create.php
/docs/mods/_core/groups/create_automatic.php
/docs/mods/_core/groups/create_manual.php
NOT DONE: - /docs/mods/_core/groups/index.php

[PATCHER]

[POLLS] 
/docs/mods/_standard/polls/tools/index.php  *** DONE
/docs/mods/_standard/polls/tools/edit.php  *** DONE
/docs/mods/_standard/polls/tools/add.php  *** DONE

[PROPERTIES] 
http://localhost/GSoC2011/docs/mods/_core/properties/course_properties.php  *** DONE

[READING LIST]
NOT DONE

[STATISTICS] 
/docs/mods/_standard/statistics/course_stats.php - remove inline styles, fix markup

[STUDENT TOOLS]
NOT DONE: /docs/mods/_standard/student_tools/instructor_index.php 



