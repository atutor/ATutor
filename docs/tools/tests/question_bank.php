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
// $Id: index.php 2326 2004-11-17 17:50:58Z heidi $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_bank');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

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
	echo _AT('test_manager');
}
echo '</h3>';

$msg->printAll();
?>

<p align="center"><br /><a href="tools/tests/index.php"><?php echo _AT('tests'); ?></a> | <?php echo _AT('question_bank'); ?> | <a href="tools/tests/question_cats.php"><?php echo _AT('question_categories'); ?></a>
</p>

<?php echo _AT('view'); ?>: 
<?php
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result	= mysql_query($sql, $db);
	echo '<select>';
	echo '<option>--'._AT('all').'--</option>';
	while ($row = mysql_fetch_array($result)) {
		echo '<option value="'.$row['category_id'].'">'.$row['title'].'</option>';
	}
	echo '</select> ';
?>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="100%" class="cyan"><?php echo _AT('questions'); ?></th>
</tr>
<?php
	//output category
?>

<tr>
	<th scope="col" class="cat"></th>
	<th scope="col" class="cat"><small><?php echo _AT('question'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('weight'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('type'); ?></small></th>

<?php $cols=6;	
if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN)) {
	echo '<th scope="col" class="cat"></th>';
	$cols++;
}
echo '</tr>';

echo '<tr>';
	echo '<td class="row1"></td>';
echo '</tr>';

echo '</table>';
echo '<br />';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>