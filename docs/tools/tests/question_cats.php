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

if ($_POST['submit'] == _AT('cancel')) {
	header('Location: question_db.php');
	exit;

} else if ($_POST['submit'] == _AT('edit')) {
	if ($_POST['category']) {
		header('Location: question_cats_manage.php?catid='.$_POST['category']);
		exit;
	} else {
		$msg->addError('NO_CAT_SELECTED');
	}

} else if ($_POST['submit'] == _AT('delete')) {
	if (isset($_POST['category'])) {
		//confirm
		header('Location: question_cats_delete.php?catid='.$_POST['category']);
		exit;

	} else {
		$msg->addError('NO_CAT_SELECTED');
	}	
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
echo '<a href="tools/tests/index.php">'._AT('test_manager').'</a>';
echo '</h3>';

echo '<h4><a href="tools/tests/question_db.php">' . _AT('question_database') . '</h4>';
$msg->addHelp('QUESTION_CATEGORIES');
$msg->printAll();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div align="center">
<span class="editorsmallbox">
	<small><img src="<?php echo $_base_path; ?>images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" height="14" width="16" /> <a href="tools/tests/question_cats_manage.php"><?php echo _AT('add'); ?></a></small>
</span>
</div>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('cats_categories'); ?> </th>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php 
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result	= mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		do { ?>
			<tr>
				<td class="row1" align="right"><input type="radio" id="cat_<?php echo $row['category_id']; ?>" name="category" value="<?php echo $row['category_id']; ?>" /></td>
				<td class="row1"><label for="cat_<?php echo $row['category_id']; ?>"><?php echo $row['title']; ?></label></td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php } while ($row = mysql_fetch_assoc($result)); ?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('edit'); ?>" class="button" name="submit" /> | <input type="submit" value="<?php echo _AT('delete'); ?>" class="button" name="submit" /> | <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="submit" /></td>
		</tr>
	<?php
	} else {
		echo '<tr><td class="row1">'._AT('cats_no_categories').'</td></tr>';
		echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	}?>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>