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

	<p align="center"><br /><a href="tools/tests/index.php"><?php echo _AT('tests'); ?></a> | <?php echo _AT('question_bank'); ?> | <a href="tools/tests/question_cats.php"><?php echo _AT('question_categories'); ?></a></p>

<?php 
echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="category_form">';
echo _AT('view_category').': '; 
$cats = array();
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result	= mysql_query($sql, $db);
echo '<select name="cat_id">';
echo '<option>--'._AT('all').'--</option>';
while ($row = mysql_fetch_assoc($result)) {
	$cats[] = $row;
	echo '<option value="'.$row['category_id'].'">'.$row['title'].'</option>';
}
echo '</select> <input type="submit" value="'._AT('view').'" name="submit" />';
echo '</form>';
?>
<br />
<form method="post" action="tools/tests/add_test_questions.php">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="100%" class="cyan"><?php echo _AT('questions'); ?></th>
</tr>
<tr>
	<th scope="col" class="cat"><small><?php echo _AT('add'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('question'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('type'); ?></small></th>
	<th scope="col" class="cat"></th>
<?php 
echo '</tr>';

$question_flag = FALSE;

//output categories
foreach ($cats as $cat) {
	//ouput questions
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND category_id=".$cat['category_id']." ORDER BY question";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		$question_flag = TRUE;
		echo '<tr>';
		echo '<td colspan="4"><strong>'.$cat['title'].'</strong></td>';
		echo '</tr>';
		do {
			echo '<tr>';
				echo '<td class="row1"><input type="checkbox" value="'.$row['question_id'].'" name="add_questions[]" id="q'.$row['question_id'].'" /></td>';
				echo '<td class="row1"><label for="q'.$row['question_id'].'"><small>';
				if (strlen($row['question']) > 45) {
					echo AT_print(substr($row['question'], 0, 43), 'tests_questions.question') . '...';
				} else {
					echo AT_print($row['question'], 'tests_questions.question');
				}

				echo '</small></label></td>';
				echo '<td class="row1" nowrap="nowrap"><small>';
				switch ($row['type']) {
					case 1:
						echo _AT('test_mc');
						break;
						
					case 2:
						echo _AT('test_tf');
						break;
			
					case 3:
						echo _AT('test_open');
						break;
					case 4:
						echo _AT('test_lk');
						break;
				}
						
				echo '</small></td>';
	
			echo '<td class="row1" nowrap="nowrap"><small>';
			switch ($row['type']) {
				case 1:
					echo '<a href="tools/tests/edit_question_multi.php?qid='.$row['question_id'].'">';
					break;
					
				case 2:
					echo '<a href="tools/tests/edit_question_tf.php?qid='.$row['question_id'].'">';
					break;
				
				case 3:
					echo '<a href="tools/tests/edit_question_long.php?qid='.$row['question_id'].'">';
					break;
				case 4:
					echo '<a href="tools/tests/edit_question_likert.php?qid='.$row['question_id'].'">';
					break;
			}

			echo _AT('edit').'</a> | ';
			echo '<a href="tools/tests/delete_question.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">'._AT('delete').'</a></small></td>';
			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';

		} while ($row = mysql_fetch_assoc($result));
	} 
}  

if (!$question_flag) {
	echo '<tr><td colspan="4" class="row1"><small><i>'._AT('no_questions_avail').'</i></small></td></tr>';
} else {
	echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';
	echo '<tr><td colspan="4" class="row1">';
	$sql    = "SELECT test_id, title FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		echo '<select name="test_id">';
		do {
			echo '<option value="'.$row['test_id'].'">'.$row['title'].'</option>';

		} while ($row = mysql_fetch_assoc($result));

		echo '</select><input type="submit" name="submit" value="[Add To Test]" class="submit" />';
	} else {
		echo 'no tests found';
	}
	echo '</td></tr>';
}

echo '</table></form>';
echo '<br />';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>