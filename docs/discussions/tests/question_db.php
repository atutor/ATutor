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

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_database');

$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

if (isset($_GET['submit_create'])) {
	header('Location: create_question_'.$_GET['question_type'].'.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/tests/index.php">'._AT('test_manager').'</a>';
}
echo '</h3>';

echo '<h4>' . _AT('question_database') . '</h4>';
$msg->printAll();
?>

<div align="center">
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
		<span class="editorsmallbox">
			<small>
			<img src="<?php echo $_base_path; ?>images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" height="14" width="16" />
			<select name="question_type" class="dropdown">
				<option value="multi"><?php echo _AT('test_mc'); ?></option>
				<option value="tf"><?php echo _AT('test_tf'); ?></option>
				<option value="long"><?php echo _AT('test_open'); ?></option>
				<option value="likert"><?php echo _AT('test_lk'); ?></option>
			</select>
			<input type="submit" name="submit_create" value="<?php echo _AT('create'); ?>" class="button2" />
			</small>
			<small>| <a href="tools/tests/question_cats.php"><?php echo _AT('cats_categories'); ?></a></small>
		</span>
	</form>
</div>

<?php $tid = 0; ?>

<?php require(AT_INCLUDE_PATH.'html/tests_questions.inc.php'); ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>