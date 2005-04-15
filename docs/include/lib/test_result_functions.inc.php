<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

// returns T/F whether or not this member can view this test:
function authenticate_test($tid) {
	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		return TRUE;
	}

	global $db;
	$sql    = "SELECT group_id FROM ".TABLE_PREFIX."tests_groups WHERE test_id=$tid";
	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result) == 0) {
		// not limited to any group; everyone has access:
		return TRUE;
	}
	while ($row = mysql_fetch_assoc($result)) {
		$sql     = "SELECT * FROM ".TABLE_PREFIX."groups_members WHERE group_id=$row[group_id] AND member_id=$_SESSION[member_id]";
		$result2 = mysql_query($sql, $db);

		if ($row2 = mysql_fetch_assoc($result2)) {
			return TRUE;
		}
	}

	return FALSE;
}

function print_result($q_text, $q_answer, $q_num, $a_num, $correct) {
	global $mark_right, $mark_wrong;

	if ($a_num == '') {
		$a_num = -1;
	}

	if ($a_num == $q_num) {
		echo '<input type="radio" checked="checked" />';
		if ($correct && ($correct != 'none')) {
			echo $mark_right;
		} else if ($correct != 'none') {
			echo $mark_wrong;
		}
		echo AT_print($q_text, 'tests_answers.answer');

	} else {
		echo '<input type="radio" disabled="disabled" />';
		echo AT_print($q_text, 'tests_answers.answer');
	}
}

function print_score($correct, $weight, $qid, $score, $put_zero = true, $disabled = false) {
	echo '<input type="text" size="5" name="scores['.$qid.']" value="';

	if ($score != '') {
		echo $score;
	/*} else if ($correct == $score){
		echo $weight;
	} else if ($correct!=$score){
		echo '0';*/
	} else if ($correct) {
		echo $weight;
	} else if ($put_zero) {
		echo '0';
	}
	echo '" style="width: 30px; font-weight: bold;" maxlength="4" '.($disabled ? 'disabled="disabled" ' : '').'/><strong>/'.floatval($weight).'</strong>';
}

function print_question_cats($cat_id = 0) {	

	global $db;

	echo '<option value="0">'._AT('cats_uncategorized').'</option>';
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'tests_questions_categories WHERE course_id='.$_SESSION['course_id'].' ORDER BY title';
	$result	= mysql_query($sql, $db);

	while ($row = mysql_fetch_array($result)) {
		echo '<option value="'.$row['category_id'].'"';
		if ($row['category_id'] == $cat_id) {
			echo 'selected="selected" ';
		}
		echo '>'.$row['title'].'</option>';
	}
}

function print_VE ($area) {
	global $_base_href;
?>
	<script type="text/javascript" language="javascript">
		document.writeln('<a onclick="javascript:window.open(\'<?php echo $_base_href; ?>tools/tests/form_editor.php?area=<?php echo $area; ?>\',\'newWin1\',\'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480\')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>');
	</script>

<?php
	//possibley add a <noscript> to filemanager
}

?>