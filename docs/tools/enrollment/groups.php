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
// $Id: question_cats.php 2517 2004-11-25 16:05:18Z heidi $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (!($result) || !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('groups');

if ($_POST['submit'] == _AT('cancel')) {
	header('Location: index.php');
	exit;

} else if ($_POST['submit'] == _AT('edit')) {
	if ($_POST['group']) {
		header('Location: groups_manage.php?gid='.$_POST['group']);
		exit;
	} else {
		$msg->addError('NO_CAT_SELECTED');
	}

} else if ($_POST['submit'] == _AT('delete')) {
	if (isset($_POST['group'])) {
		//confirm
		header('Location: groups_delete.php?gid='.$_POST['group']);
		exit;

	} else {
		$msg->addError('NO_CAT_SELECTED');
	}	
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

$msg->printAll();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div align="center">
<span class="editorsmallbox">
	<small><img src="<?php echo $_base_path; ?>images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" height="14" width="16" /> <a href="tools/enrollment/groups_manage.php"><?php echo _AT('add'); ?></a></small>
</span>
</div>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('groups'); ?> </th>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php 
	$sql	= "SELECT * FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		do { ?>
			<tr>
				<td class="row1" align="right"><input type="radio" id="g_<?php echo $row['group_id']; ?>" name="group" value="<?php echo $row['group_id']; ?>" /></td>
				<td class="row1"><label for="g_<?php echo $row['group_id']; ?>"><?php echo $row['title']; ?></label></td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php } while ($row = mysql_fetch_assoc($result)); ?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('edit'); ?>" class="button" name="submit" /> | <input type="submit" value="<?php echo _AT('delete'); ?>" class="button" name="submit" /> | <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="submit" /></td>
		</tr>
	<?php
	} else {
		echo '<tr><td class="row1">'._AT('groups_no_groups').'</td></tr>';
		echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	}?>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>