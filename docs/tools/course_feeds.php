<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}


/* make sure we own this course that we're approving for! */

if (!(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) && !(authenticate(AT_PRIV_COURSE_FEEDS, AT_PRIV_RETURN))) {
	$msg->printErrors('PREFS_NO_ACCESS');
	exit;
}

if ($_POST['cancel']) {
	header('Location: index.php');
	exit;
}

$title = _AT('course_feedsl');
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/course_feeds-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('course_feeds');
}
echo '</h3>'."\n";

/* we own this course! */
$msg->printErrors();
?>
<form action="">
<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
<tr><th></th><th><?php echo _AT('feed');  ?></th><th><?php echo _AT('public');  ?></th><th><?php echo _AT('private');  ?></th></tr>
<tr><td><input type="checkbox" name="forum"></td><td><?php echo _AT('forum');  ?></td><td></td></tr>
</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
