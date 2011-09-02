******************************************************************************************
Theme:		1.6.4 Mobile Theme
Date:		August 2011
******************************************************************************************


Installing:	 See section "Installing a New Theme" in the themes_readme.txt file located in the themes/ top directory.
Licence:	Falls under the GPL agreement.  See http://www.gnu.org/copyleft/gpl.html.

==============================================================================
What's new: 
==============================================================================

/mobile.css 
* style for android, iphone, & ipod rolled into one stylesheet
* improved subnavigation and in-course navigation

/tablet.css 
* new style for tablet devices, beginning with -webkit browsers
* generic CSS used to broaden browser support

include/header.tmpl.php
* accessibility: increased support for ARIA roles that Safari recognizes. Note: ARIA roles create HTML validation errors.

==================================================================================
Known Issues / More work needed
==================================================================================

why isn't simplified-desktop in svn? 

Outstanding templates to be created: 
* see "TEMPLATES - CREATED & OUTSTANDING" for a list of my progress &  "Omitted from mobile/" for a list of work that needs to be done. 
 
Towards a simplified desktop theme: 
* develop a desktop theme based on tablet.css (harder) 
* develop a desktop theme based on mobile.css (easier) & do a final update to ensure generic CSS is used
** update to -moz rules
* erase commented out styles from mobile.css and tablet.css and re-order where necessary

Simplify or remove this rule in mobile.css and tablet.css
* navigation-bar-button-content

Tablet bug? 
In Firefox, the "Home" and "Previous/Next" buttons are the wrong height. Test on the tablet
then see if it can be reproduced there before fixing. 

Aesthetic improvements, mobile: 
* Resume, Previous, Next on mobile should highlight as a block (outstanding)
* on activation should highlight as a block (done-AUG27) 
* the Subnavigation div should highlight as a block (done-AUG27)
* Instructor user: (done-AUG27) /docs/mods/_standard/statistics/course_stats.php - (template now includes graph)


* "0004796: Student user's Preferences template won't display"
** see: http://atutor.ca/atutor/mantis/view.php?id=4796
** see: http://atutor.ca/atutor/mantis/view.php?id=4679


Mobile FSS 
* "Activation hightlighting is visible on the desktop but not the on the mobile device"
** see: http://issues.fluidproject.org/browse/FLUID-4313
** both arrows and background color don't highlight.
** affects .fl-lists, including:
*** the "Navigation" menus on mobiles and tablets after a link is highlighted 
*** docs/users/browse.php in mobile and tablets

Mark McLaren's moz.css
https://github.com/fluid-project/infusion/commit/25ad6755ef78347b414d60bd4037a0f197f9d09d#diff-7
==================================================================================
Omitted from mobile/
==================================================================================
Administrator user: 
*Patcher 
*/docs/mods/_core/cats_categories/admin/course_categories.php
*/docs/mods/_core/enrolment/admin/privileges.php 
*/docs/mods/_core/modules/install_modules.php
*/docs/mods/_core/languages/language_editor.php

Instructor user: 
*/docs/mods/_standard/assignments/add_assignment.php
*Course Tools
*/docs/mods/_core/enrolment/create_course_list.php
*/docs/mods/_core/enrolment/privileges.php
*mods/_core/file_manager/index.php
*file manager -- removed for mobile 
*reading list
*groups
*gradebook
*glossary
** /docs/mods/_core/glossary/tools/index.php ** NOT DONE
** docs/mods/_core/glossary/tools/add.php ** DONE - August 31st
*patcher
*student tools

===================================================================================================
Accessibility notes, features, & validation issues 
===================================================================================================
*Newer versions of iPods, iPads, & iPhones have limited support for WAI-ARIA. I deployed the roles that were supported. 
VoiceOver users can enable or disable speaking, for example, of various WAI-ARIA roles. Here is Apple's documentation:
 http://developer.apple.com/library/safari/#documentation/appleapplications/reference/SafariHTMLRef/Articles/AccessibilityRoles.html

WCAG AA
I primarily used WCAG to guide the evolution of content generated from the header and footer. 
Below are Success Criteria that apply to my work, and I have listed criteria that don't pass
 or that may need more attention. Further Success Criteria apply to the LMS but would depend on, 
for example, course content used.  I checked for WCAG as a part of my AChecker workflow 
(to WCAG AA) along with validating markup, and manually on the following capstone pages: 
**

Applicable Success Criteria 
1.1.1 Non-text Content***
1.3.1 Info and Relationships 
	Note: should fieldset/legends and onkeydown be added to these pages?
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
 

1.3.2 Meaningful Sequence
Mobile - passes, but Tablet - fails. Logged into a course as a student user, the DOM order should match the visual order.
I have listed this as an issue.  

1.3.3 Sensory Characteristics
1.4.3 Contrast (Minimum)
1.4.4 Resize text
-- Applies but unsure how to test using the zoom feature (i.e. to what proportion it magnifies to). 
-- Increasing text size with finger gestures is disabled, but using Apple's zoom feature, text appears readable.

2.1.1 Keyboard
**Mobile - passes, but Tablet - fails (Navigation button). I have listed this as an issue. 
2.1.2 No Keyboard Trap
2.4.1 Bypass Blocks
**Note: This passes for both mobile & tablet because heading groupings are used at the beginning of content. 
**Also, skip-links are working with VoiceOver now on tablet.
2.4.3 Focus Order
2.4.4 Link Purpose (In Context)
2.4.5 Multiple Ways
2.4.6 Headings and Labels
2.4.7 Focus Visible
3.1.1 Language of Page
3.2.1 On Focus
**Is this violated by the pop-up "guide" button in the mobile and tablet devices? 

3.2.3 Consistent Navigation
3.2.4 Consistent Identification
3.3.2 Labels or Instructions
3.3.3 Error Suggestion - already handled 
4.1.1 Parsing, 4.1.2 Name, Role, Value

==============================================================================
TEMPLATES - CREATED & OUTSTANDING
==============================================================================
ADMINISTRATORS: MOBILE -------------------------------------------------------

NOTE there are 4 errors in HTML validator due to using an ARIA role. 

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
3.  /docs/mods/_standard/forums/admin/forums.php ****DONE HTML (note: lacks fieldset, added onkeydown)
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
4.  /docs/mods/_core/languages/language.php -- **** DONE, VALID WCAG, HELP on HTML (CHANNEL BUG)  *** valid HTML except for fieldset
5.  /docs/mods/_core/languages/language_editor.php -  **** NOT DONE NEEDS TEMPLATING 
6.  /docs/mods/_standard/rss_feeds/preview.php
7.  /docs/mods/_standard/rss_feeds/edit_feed.php **** DONE / WCAG AA / Valid HTML
8.  /docs/mods/_standard/rss_feeds/index.php **** DONE / WCAG AA / Valid HTML (note: lacks fieldset, added onkeydown)
9. /docs/mods/_standard/rss_feeds/add_feed.php **** DONE / WCAG AA / Valid HTML
10. /docs/mods/_standard/tile_search/admin/module_setup.php **** DONE / HELP WCAG / HELP HTML (CHANNEL BUG)
11. /docs/mods/_standard/google_search/admin/module_prefs.php **** DONE, VALID WCAG, HTML good (except for legacy <b> tag)
12. /docs/mods/_standard/social/admin/delete_applications.php ==  already templated
13. /docs/mods/_standard/social/index_admin.php **** DONE / WCAG AA / Valid HTML
14. /docs/admin/cron_config.php **** DONE / WCAG AA / Valid HTML -- wrap bug. 
15. /docs/admin/error_logging.php

INSTRUCTORS: MOBILE ---------------------------------------------------------------------------------- 
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
/docs/mods/_core/editor/edit_content_folder.php?cid=240 **** DONE 
/docs/mods/_standard/tracker/tools/page_student_stats.php **** DONE 
/docs/mods/_standard/tracker/tools/index.php  **** DONE 
/docs/mods/_standard/tracker/tools/student_usage.php **** DONE 
/docs/mods/_standard/tracker/tools/reset.php ***** DONE 
/docs/mods/_core/editor/add_content.php **** NOT DONE, remove for mobile? 
/docs/mods/_core/editor/edit_content.php? **** NOT DONE, remove for mobile? (link from /index.php should be removed)

[COURSE EMAIL] 
http://localhost/GSoC2011/docs/mods/_standard/course_email/course_email.php **** DONE 

[ENROLLMENT]
/docs/mods/_core/enrolment/export_course_list.php **** DONE 
/docs/mods/_core/enrolment/import_course_list.php **** DONE 
/docs/mods/_core/enrolment/create_course_list.php *** Remove for mobile
/docs/mods/_core/enrolment/index.php **** DONE 
/docs/mods/_core/enrolment/privileges.php   **** NOT DONE


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


[POLLS] 
/docs/mods/_standard/polls/tools/index.php  *** DONE
/docs/mods/_standard/polls/tools/edit.php  *** DONE
/docs/mods/_standard/polls/tools/add.php  *** DONE

[PROPERTIES] 
http://localhost/GSoC2011/docs/mods/_core/properties/course_properties.php  *** DONE

[STUDENT TOOLS]
NOT DONE: /docs/mods/_standard/student_tools/instructor_index.php 

----------------------------------------------------------------------------------------
TABLET TEMPLATE: STUDENT 
*Note: there are 4 ARIA-errors! 

/docs/login.php - WCAG AA / Valid HTML
/docs/browse.php - WCAG AA / Valid HTML - 6 errors - ARIA-related
/docs/users/profile.php  - WCAG AA / Valid HTML
/docs/users/preferences.php - WCAG AA / Valid HTML
/docs/users/index.php - WCAG AA / Problem with HTML validation, likely to do with the "Things Current" list
/docs/registration.php
