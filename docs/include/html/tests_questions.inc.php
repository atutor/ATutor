<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

$msg =& new Message($savant);

$cats = array();
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result	= mysql_query($sql, $db);
$cats[] = array('title' => _AT('cats_uncategorized'), 'category_id' => 0);
while ($row = mysql_fetch_assoc($result)) {
	$cats[] = $row;
}

	$cols = 4;
?>

<form method="post" action="tools/tests/add_test_questions_confirm.php" name="form">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th scope="col" class="cat" colspan="2"><small><?php echo _AT('question'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('type'); ?></small></th>
	<th scope="col" class="cat"></th>
</tr>

<?php

$question_flag = FALSE;

//output categories
foreach ($cats as $cat) {
	//ouput questions
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND category_id=".$cat['category_id']." ORDER BY question";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$question_flag = TRUE;
		echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';
		echo '<tr>';
		echo '<td colspan="'.$cols.'">';

		if ($tid) {
			echo '<label><input type="checkbox" name="cat'.$cat['category_id'].'" id="cat'.$cat['category_id'].'" onclick="javascript:selectCat('.$cat['category_id'].', this);"> <strong>'.$cat['title'].'</strong></label>';
		} else {
			echo '<strong>'.$cat['title'].'</strong>';
		}
		echo '</td>';
		echo '</tr>';

		do {
			echo '<tr><td height="1" class="row2" colspan="'.$cols.'"></td></tr>';
			echo '<tr>';
			echo '<td class="row1">';
			if ($tid) {
				echo '<input type="checkbox" value="'.$row['question_id'].'" name="add_questions['.$cat['category_id'].'][]" id="q'.$row['question_id'].'" />';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
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

			if (!$tid) {				
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
				echo '<a href="tools/tests/delete_question.php?qid='.$row['question_id'].'">'._AT('delete').'</a> | ';
			}
			echo '<a href="tools/tests/preview_question.php?qid='.$row['question_id'].'">'._AT('preview').'</a>';
			echo '</small></td>';
			echo '</tr>';

		} while ($row = mysql_fetch_assoc($result));
	} 
}  

if (!$question_flag) {
	echo '<tr><td colspan="'.$cols.'" class="row1"><small><i>'._AT('no_questions_avail').'</i></small></td></tr>';
} else if ($tid) {
	echo '<tr><td height="1" class="row2" colspan="'.$cols.'"></td></tr>';
	echo '<tr><td height="1" class="row2" colspan="'.$cols.'"></td></tr>';
	echo '<tr><td colspan="'.$cols.'" class="row1">';
	echo '<input type="hidden" name="tid" value="'.$tid.'" />';
	echo '<input type="submit" name="submit" value="'._AT('add_to_test_survey').'" class="button" />';
	echo ' | <input type="submit" name="cancel" value="'._AT('cancel').'" class="button" />';
	echo '</td></tr>';
}

?>

</table>
</form>
<br />

<script language="javascript">
	
	function selectCat(catID, cat) {
		for (var i=0;i<document.form.elements.length;i++) {
			var e = document.form.elements[i];
			if ((e.name == 'add_questions[' + catID + '][]') && (e.type=='checkbox'))
				e.checked = cat.checked;
		}
	}
</script>