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
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

authenticate(AT_PRIV_TEST_CREATE);

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('cats_categories');
$_section[3][1] = 'tools/tests/question_cats.php';
$_section[4][0] = _AT('cats_category');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_cats.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['title'] = trim($_POST['title']);

	if (!empty($_POST['title']) && !isset($_POST['catid'])) {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions_categories VALUES (0, $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('CAT_ADDED');
		header('Location: question_cats.php');
		exit;
	} else if (!empty($_POST['title']) && isset($_POST['catid']))  {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "REPLACE INTO ".TABLE_PREFIX."tests_questions_categories VALUES ($_POST[catid], $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('CAT_UPDATE_SUCCESSFUL');
		header('Location: question_cats.php');
		exit;
	} else {
		$msg->addError('CAT_NO_NAME');
	}
}

if (isset($_GET['catid'])) {
	$sql = "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE category_id=$_GET[catid]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$_POST['title'] = $row['title'];
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
echo '</h3>';

echo '<h4><a href="tools/tests/question_db.php">' . _AT('question_database') . '</h4>';

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
if (isset($_REQUEST['catid'])) {
	echo '<input type="hidden" value="'.$_REQUEST['catid'].'" name="catid" />';
}
?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('cats_category'); ?> </th>
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