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

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_bank');
$_section[2][1] = 'tools/tests/question_bank.php';
$_section[3][0] = _AT('questions_cats');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit'])) {

	if ($_POST['category']) {
		header('Location: question_cats_manage.php?cid='.$_POST['category']);
		exit;
	} else {
		$msg->addError('NOT_SELECTED');
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

?>
<p align="center"><br /><a href="tools/tests/index.php"><?php echo _AT('tests'); ?></a> | <a href="tools/tests/question_bank.php"><?php echo _AT('question_bank'); ?></a> | <?php echo _AT('question_categories'); ?></p>

<p align="center"><a href="tools/tests/question_cats_manage.php"><?php echo _AT('cats_add_categories'); ?></a></p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('question_categories'); ?> </th>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php 
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories ORDER BY title";
	$result	= mysql_query($sql, $db);

	while ($row = mysql_fetch_array($result)) {
?>
	<tr>
		<td class="row1"><input type="radio" id="cat_<?php echo $row['category_id']; ?>" name="category" value="<?php echo $row['category_id']; ?>" /></td>
		<td class="row1"><label for="cat_<?php echo $row['category_id']; ?>"><?php echo $row['title']; ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } ?>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('edit'); ?>" class="button" name="submit" accesskey="s" /> | <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
</tr>
</table>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>