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

$page = 'courses';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/forums.php');
	exit;
} else if (isset($_POST['add_forum'])) {
	if (empty($_POST['title'])) {
		$msg->addError('TITLE_EMPTY');
	} 

	if (empty($_POST['courses'])) {
		$msg->addError('NO_COURSE_SELECTED');
	} 

	if (!($msg->containsErrors())) {
		//add forum
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums (title, description) VALUES ('" . $_POST['title'] . "','" . $_POST['description'] ."')";
		$result	= mysql_query($sql, $db);
		$forum_id = mysql_insert_id($db);

		//for each course, add an entry to the forums_courses table
		foreach ($_POST['courses'] as $course) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (" . $forum_id . "," . $course . ")";
			$result	= mysql_query($sql, $db);
		}

		$msg->addFeedback('FORUM_ADDED');
		if($course =="0"){
			$msg->addFeedback('FORUM_POSTING');
		}
		header('Location: '.$_base_href.'admin/forums.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
echo '<h3>'._AT('add_forum').'</h3><br />';

$msg->printErrors();
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php  echo _AT('forum'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><?php print_popup_help(ADD_FORUM_MINI_ADMIN); ?><b><label for="title"><?php  echo _AT('title'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="title" class="formfield" size="40" id="title" value="<?php echo $_POST['title']; ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php echo _AT('description'); ?>:</label></b></td>
	<td class="row1"><textarea name="description" cols="45" rows="5" class="formfield" id="body" wrap="wrap"><?php echo $_POST['description']; ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php echo _AT('courses'); ?>:</label></b></td>
	<td class="row1">
	<select name="courses[]" multiple="multiple" size="5">
	<?php
		$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			echo '<option value="'.$row['course_id'].'">'.$row['title'].'</option>';		
		}
	?>
	</select>
	<br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('submit'); ?> [Alt-s]" class="button" accesskey="s"> | <input type="submit" name="cancel" value="<?php  echo _AT('cancel'); ?>" class="button"></td>
</tr>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>