<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: questions.php 2326 2004-11-17 17:50:58Z heidi $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

authenticate(AT_PRIV_TEST_CREATE);

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('groups');
$_section[2][1] = 'tools/enrollment/groups.php';

if ($_REQUEST['gid']) {
	$_section[3][0] = _AT('edit');
} else {
	$_section[3][0] = _AT('add');
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: groups.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['title'] = trim($_POST['title']);

	if (!empty($_POST['title']) && !isset($_POST['gid'])) {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "INSERT INTO ".TABLE_PREFIX."groups VALUES (0, $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GROUP_ADDED');
		header('Location: groups.php');
		exit;
	} else if (!empty($_POST['title']) && isset($_POST['gid']))  {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "REPLACE INTO ".TABLE_PREFIX."groups VALUES ($_POST[gid], $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GROUP_UPDATED');
		header('Location: groups.php');
		exit;
	} else {
		$msg->addError('GROUP_NO_NAME');
	}
}

if (isset($_GET['gid'])) {
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$_GET[gid]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$_POST['title'] = $row['title'];
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
} 
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/enrollment/">'._AT('course_enrolment').'</a>';
}
echo '</h3>';

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
if (isset($_REQUEST['gid'])) {
	echo '<input type="hidden" value="'.$_REQUEST['gid'].'" name="gid" />';
}
?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('groups'); ?> </th>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="cat"><b><?php echo _AT('title'); ?>:</b></label></td>
	<td class="row1"><input type="text" name="title" id="cat" value="<?php echo $_POST['title']; ?>" class="formfield" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('save'); ?> Alt-s" class="button" name="submit" accesskey="s" /> | <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
</tr>
</table>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>