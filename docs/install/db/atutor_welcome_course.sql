# SQL for the Welcome Course

# create the Welcome Course
INSERT INTO `courses` VALUES (0, 1, 0, 'top', 'public', NOW(), 'Welcome Course', '', 0, '-2', '-3', 0, 'a:27:{s:10:"PREF_STACK";a:6:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:13:"PREF_OVERRIDE";i:1;s:11:"PREF_SEARCH";i:1;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:9:"PREF_EDIT";i:1;s:10:"PREF_LOCAL";i:0;s:13:"PREF_GLOSSARY";i:0;s:12:"PREF_RELATED";i:0;}', '', '', '', 'off');

# create content for the Welcome Course
INSERT INTO `content` VALUES (1, 1, 0, 1, NOW(), 0, 1, NOW(), '', '', 'Welcome To ATutor', 'This is just a blank content page. You can edit or delete this page by enabling the Editor and using the options directly above.',0);

# enroll into the Welcome Course
INSERT INTO `course_enrollment` VALUES (1, 1, 'y', 0);

# create forum for Welcome Course
INSERT INTO `forums` VALUES (1, 1, 'General Discussion', '');

# add a single thread
INSERT INTO `forums_threads` VALUES (1, 0, 1, 1, 1, 'instructor', NOW(), 0, 'Welcome', 'Welcome to the General Discussion forum.', NOW(), 0, 0);

# create news for Welcome Course
INSERT INTO `news` VALUES (1, 1, 1, NOW(), 1, 'Welcome To ATutor', 'This is some default content. See the <a href="help/about_help.php">About ATutor Help</a> for sources of information about using ATutor.');

# create link category
INSERT INTO `resource_categories` VALUES (1,1,'ATutor Links',NULL);

# create links for ATutor.ca and ATRC
INSERT INTO `resource_links` VALUES ('1', '1', 'http://atutor.ca', 'ATutor.ca', 'ATutor is an Open Source Web-based Learning Content Management System (LCMS) designed with accessibility and adaptability in mind.', '1', '', '', NOW(), '0');

INSERT INTO `resource_links` VALUES ('2', '1', 'http://www.utoronto.ca/atrc/', 'Adaptive Technology Resource Centre', 'The Adaptive Technology Resource Centre advances information technology that is accessible to all; through research, development, education, proactive design consultation and direct service.', '1', '', '', NOW(), '0');

# create example test
INSERT INTO `tests` VALUES ('1', '1', 'About ATutor Test', '0', NOW(), NOW(), '1', '0', 'This is an example test.');

# create some test questions
INSERT INTO `tests_questions` VALUES (1, 1, 1, 0, 1, 5, 1, '', 'What does the "A" in ATutor stand for?', 'Apple', 'Academic', 'Accessible', 'Amazing', 'Adaptive', 'both #3 and #5', '', '', '', '', 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0);

INSERT INTO `tests_questions` VALUES (2, 1, 1, 0, 3, 5, 1, '', 'What is the name of ATutor\'s official instructional course/documentation?', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1);

INSERT INTO `tests_questions` VALUES (3, 1, 1, 0, 2, 3, 1, '', 'ATutor is an Open Source project.', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
 