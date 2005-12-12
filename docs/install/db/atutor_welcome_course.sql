# SQL for the Welcome Course

# create the Welcome Course
INSERT INTO `courses` VALUES (1, 1, 0, 'top', 'public', NOW(), 'Welcome Course', '', 0, '-2', '-3', 0, '', '', '', '', '', '', 'en', 0, 'books.jpg', 'forum/list.php|glossary/index.php|chat/index.php|tile.php|faq/index.php|links/index.php|tools/my_tests.php|sitemap.php|export.php|my_stats.php|polls/index.php|directory.php','forum/list.php|glossary/index.php','menu_menu|related_topics|users_online|glossary|search|poll|posts');

# create content for the Welcome Course
INSERT INTO `content` VALUES (1, 1, 0, 1, NOW(), 0, 1, NOW(), '', '', 'Welcome To ATutor', 'This is just a blank content page. Use the Edit Content link to edit this page. You can manage this course by accessing the Manage section.',0);

# enroll into the Welcome Course
INSERT INTO `course_enrollment` VALUES (1, 1, 'y', 0, '', 0);

# create forum for Welcome Course
INSERT INTO `forums` VALUES (1, 'General Discussion', '', 1, 1, NOW());
INSERT INTO `forums_courses` VALUES (1, 1);


# add a single thread
INSERT INTO `forums_threads` VALUES (1, 0, 1, 1, 'instructor', NOW(), 1, 'Welcome', 'Welcome to the General Discussion forum.', NOW(), 0, 0);

# create news for Welcome Course
INSERT INTO `news` VALUES (1, 1, 1, NOW(), 1, 'Welcome To ATutor', 'This is a welcome announcement. You can access additional help by using the Help link available throughout ATutor.');

# create link category
INSERT INTO `resource_categories` VALUES (1,1,'ATutor Links',NULL);

# create links for ATutor.ca and ATRC
INSERT INTO `resource_links` VALUES ('1', '1', 'http://atutor.ca', 'ATutor.ca', 'ATutor is an Open Source Web-based Learning Content Management System (LCMS) designed with accessibility and adaptability in mind.', '1', '', '', NOW(), '0');

INSERT INTO `resource_links` VALUES ('2', '1', 'http://www.utoronto.ca/atrc/', 'Adaptive Technology Resource Centre', 'The Adaptive Technology Resource Centre advances information technology that is accessible to all; through research, development, education, proactive design consultation and direct service.', '1', '', '', NOW(), '0');

# create example test
INSERT INTO `tests` VALUES ('1', '1', 'About ATutor Test', '0', NOW(), '2005-12-30 12:00:00', '1', '0', 'This is an example test.', 0, 0, 0, 0, 0, 0);

# create some test questions
INSERT INTO `tests_questions` VALUES (1, 1, 1, 0, 1, 5, 1, '', 'What does the "A" in ATutor stand for?', 'Apple', 'Academic', 'Accessible', 'Amazing', 'Adaptive', 'both #3 and #5', '', '', '', '', 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0);

INSERT INTO `tests_questions` VALUES (2, 1, 1, 0, 3, 5, 1, '', 'What is the name of ATutor\'s official instructional course/documentation?', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0);

INSERT INTO `tests_questions` VALUES (3, 1, 1, 0, 2, 3, 1, '', 'ATutor is an Open Source project.', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
 