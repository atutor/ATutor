<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'sitemap';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('sitemap');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif" border="0" vspace="2" class="menuimageh2" width="41" height="40" alt="" /></a> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/index.php?g=11">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/sitemap-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('sitemap');
}
echo '</h3>';

echo '<p><small><img src="themes/default/images/nav-home.gif" alt="" class="menuimage8" /> <a href="index.php">'._AT('home').'</a><br />';

$contentManager->printSiteMapMenu();

// Tools
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="themes/default/images/nav-tools.gif" alt="" class="menuimage8" /> <a href="tools/">'._AT('tools').'</a>';


// If logged in as instructor or admin (tree would look slightly different)
if ($_SESSION['privileges'] || authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
	$spacer = "vertline";
} else {
	$spacer = "space";
}

// Student Tools
echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
if (strcmp ($spacer, "vertline") == 0) {
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
} else {
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
}
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /> '._AT('student').' '._AT('tools');

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/icons/default/search-small.gif" alt="" class="menuimage8" /> <a href="users/search.php?g=20">'._AT('search').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/toc.gif" alt="" class="menuimage8" /> <a href="tools/sitemap/">'._AT('sitemap').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/glossary.gif" alt="" class="menuimage8" /> <a href="glossary/">'._AT('glossary').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/icons/default/package-small.gif" alt="" class="menuimage8" /> <a href="tools/ims/index.php?g=27">'._AT('export_content').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/icons/default/course-tracker-small.gif" alt="" class="menuimage8" /> <a href="tools/tracker.php?g=28">'._AT('my_tracker').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_'.$spacer.'.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
echo '<img src="images/icons/default/my-tests-small.gif" alt="" class="menuimage8" /> <a href="tools/my_tests.php?g=32">'._AT('my_tests').'</a>';


// Instructor Tools
if (strcmp ($spacer, "vertline") == 0) {
	// Determine where the tree ends based on priveledge:
	$priv1 = "split";
	$priv2 = "split";
	$priv3 = "split";
	$priv4 = "split";
	$priv5 = "split";
	// Check priveledges backwards to find the last group that gets displayed
	if (authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) {}
	else if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) { $priv5 = "end"; }
	else if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN) || authenticate(AT_PRIV_TEST_MARK, AT_PRIV_RETURN)) { $priv4 = "end"; }
	else if (authenticate(AT_PRIV_FILES, AT_PRIV_RETURN)){ $priv3 = "end"; }
	else if (authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) { $priv2 = "end"; }
	else { $priv1 = "end"; }

	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /> '._AT('instructor_tools');

	if (authenticate(AT_PRIV_COURSE_EMAIL, AT_PRIV_RETURN)) {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv1.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/course_mail-small.gif" alt="" class="menuimage8" /> <a href="tools/course_email.php">'._AT('course_email').'</a>';
	}
	if (authenticate(AT_PRIV_FEEDS, AT_PRIV_RETURN)) {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv1.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/course_feeds-small.gif" alt="" class="menuimage8" /> <a href="tools/course_feeds.php">'._AT('course_feeds').'</a>';
	}
	if (authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv2.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/enrol_mng-small.gif" alt="" class="menuimage8" /> <a href="tools/enroll_admin.php">'._AT('course_enrolment').'</a>';
	}

	if (authenticate(AT_PRIV_FILES, AT_PRIV_RETURN)){
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv3.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/file-manager-small.gif" alt="" class="menuimage8" /> <a href="tools/file_manager.php">'._AT('file_manager').'</a>';
	}

	if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN) || authenticate(AT_PRIV_TEST_MARK, AT_PRIV_RETURN)) {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv4.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/test-manager-small.gif" alt="" class="menuimage8" /> <a href="tools/tests/">'._AT('test_manager').'</a>';
	}

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) { 
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/package-small.gif" alt="" class="menuimage8" /> <a href="tools/ims/">'._AT('content_packaging').'</a>';

		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/backup-small.gif" alt="" class="menuimage8" /> <a href="tools/backup/">'._AT('backup_course').'</a>';

		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/course-tracker-small.gif" alt="" class="menuimage8" /> <a href="tools/course_tracker.php">'._AT('course_tracker').'</a>';

		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_'.$priv5.'.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/course-properties-small.gif" alt="" class="menuimage8" /> <a href="tools/course_properties.php">'._AT('course_properties').'</a>';
	}

	if (authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) { 
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/edit-preferences-small.gif" alt="" class="menuimage8" /> <a href="tools/course_preferences.php">'._AT('course_default_prefs').'</a>';

		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/banner-small.gif" alt="" class="menuimage8" /> <a href="tools/banner.php">'._AT('course_banner').'</a>';

		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo '<img src="images/icons/default/copyright-small.gif" alt="" class="menuimage8" /> <a href="tools/edit_header.php">'._AT('course_copyright2').'</a>';
	}
}

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="themes/default/images/nav-resources.gif" alt="" class="menuimage8" />  <a href="resources/">'._AT('resources').'</a>';
echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /><img src="images/icons/default/links-small.gif" alt="" class="menuimage8" /> <a href="resources/links/">'._AT('links_database').'</a>';
echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /><img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /><img src="images/icons/default/search_tile-small.gif" alt="" class="menuimage8" /> <a href="resources/tile/index.php?g=29">'._AT('tile_search').'</a>';

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="themes/default/images/nav-discussions.gif" alt="" class="menuimage8" />  <a href="discussions/">'._AT('discussions').'</a>';
echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /><img src="images/icons/default/forum-small.gif" alt="" class="menuimage8" /> <a href="forum/list.php">'._AT('forums').'</a>';

$forums = get_forums($_SESSION['course_id']);

$num_forums = count($forums);

if (is_array($forums)) {
	foreach ($forums as $row) {
		$count++;
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_space.gif" alt="" class="menuimage8" />';

		if ($count < $num_forums) {
			echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
		} else {
			echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
		}
		echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" />';
		echo ' <a href="forum/index.php?fid='.$row['forum_id'].'">'.AT_print($row['title'], 'forums.title').'</a>';
	} 
} else {
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
	echo _AT('no_forums');
}

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /><img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="menuimage8" /><img src="images/icons/default/chat-small.gif" alt="" class="menuimage8" /> <a href="discussions/achat/">'._AT('chat').'</a>';

if (defined('AC_PATH') || AC_PATH) {
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /><img src="themes/default/images/nav-acollab.gif" alt="" class="menuimage8" /> <a  href="'.$_base_path . 'acollab/bounce.php">'._AT('acollab').'</a>';
}

echo '<br />';
echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /><img src="images/help3.gif" alt="" class="menuimage8" /> <a href="help/">'._AT('help').'</a>';

echo '</small></p>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>