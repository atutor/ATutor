<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

$cats = array();
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result	= mysql_query($sql, $db);
$cats[] = array('title' => _AT('cats_uncategorized'), 'category_id' => 0);
while ($row = mysql_fetch_assoc($result)) {
	$cats[] = $row;
}

	$cols = 3;
?>
<?php if ($tid): ?>
	<form method="post" action="tools/tests/add_test_questions_confirm.php" name="form">
<?php else: ?>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<?php endif; ?>
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('type'); ?></th>
</tr>
</thead>
<tfoot>
<?php if ($tid): ?>
	<tr>
		<td colspan="3">
			<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
			<input type="submit" name="submit" value="<?php echo _AT('add_to_test_survey'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="3">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
			<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		</td>
	</tr>
<?php endif; ?>
</tfoot>
<tbody>
<?php

$question_flag = FALSE;

//output categories
foreach ($cats as $cat) {
	//ouput questions
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND category_id=".$cat['category_id']." ORDER BY question";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$question_flag = TRUE;
		echo '<tr>';
		echo '<th colspan="'.$cols.'">';

		echo '<input type="checkbox" name="cat'.$cat['category_id'].'" id="cat'.$cat['category_id'].'" onclick="javascript:selectCat('.$cat['category_id'].', this);" /><label for="cat'.$cat['category_id'].'">'.$cat['title'].'</label>';
		echo '</th>';
		echo '</tr>';

		do {
			echo '<tr onmousedown="document.form[\'q' . $row['question_id'] . '\'].checked = !document.form[\'q' . $row['question_id'] . '\'].checked;">';
			echo '<td>';
			echo '<input type="checkbox" value="'.$row['question_id'].'|'.$row['type'].'" name="questions['.$cat['category_id'].'][]" id="q'.$row['question_id'].'" onmouseup="this.checked=!this.checked" /></td>';
			echo '<td>';
			if (strlen($row['question']) > 45) {
				echo AT_print(substr(htmlentities($row['question']), 0, 43), 'tests_questions.question') . '&hellip;';
			} else {
				echo AT_print(htmlentities($row['question']), 'tests_questions.question');
			}

			echo '</td>';
			echo '<td>';
			switch ($row['type']) {
				case AT_TESTS_MC:
					echo _AT('test_mc');
					break;
					
				case AT_TESTS_TF:
					echo _AT('test_tf');
					break;
			
				case AT_TESTS_LONG:
					echo _AT('test_open');
					break;
				case AT_TESTS_LIKERT:
					echo _AT('test_lk');
					break;
				case AT_TESTS_MATCHING:
					echo _AT('test_matching');
					break;
				case AT_TESTS_ORDERING:
					echo _AT('test_ordering');
					break;
			}
						
			echo '</td>';
			
			echo '</tr>';

		} while ($row = mysql_fetch_assoc($result));
	} 
}  
if (!$question_flag) {
	echo '<tr><td colspan="'.$cols.'">'._AT('none_found').'</td></tr>';
}
?>
</tbody>
</table>
</form>

<script language="javascript" type="text/javascript">
// <!--
	function selectCat(catID, cat) {
		for (var i=0;i<document.form.elements.length;i++) {
			var e = document.form.elements[i];
			if ((e.name == 'questions[' + catID + '][]') && (e.type=='checkbox'))
				e.checked = cat.checked;
		}
	}
// -->
</script>