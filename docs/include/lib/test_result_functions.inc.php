<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
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
	if (!$_SESSION['enroll']) {
		return FALSE;
	}
	global $db;

	$sql    = "SELECT approved FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$_SESSION[course_id] AND approved='y'";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))) {
		return FALSE;
	}

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

// text - the text to display
// checked - whether this option should be checked or not
// correct_choice - FALSE: false/negative choice, TRUE: true/correct choice (from the test), or null for no correct/wrong choice (likert)
function print_result($text, $checked, $correct_choice = null) {
	global $_base_path;

	static $right, $wrong, $empty, $check_empty, $check_check;

	if (!isset($right)) {
		$right = ' <img src="'.$_base_path.'images/checkmark.gif" alt="'._AT('correct_answer').'" title="'._AT('correct_answer').'" height="16" width="16" style="vertical-align: middle" />';
		$wrong = ' <img src="'.$_base_path.'images/x.gif" alt="'._AT('wrong_answer').'" title="'._AT('wrong_answer').'" height="16" width="16" style="vertical-align: middle" />';
		$empty = ' <img src="'.$_base_path.'images/clr.gif" alt="" title="" height="16" width="16" style="vertical-align: middle" />';
		$check_empty = ' <img src="'.$_base_path.'images/checkbox_empty.gif" alt="'._AT('unchecked').'" title="'._AT('unchecked').'" height="13" width="13" style="vertical-align: middle" /> ';
		$check_check = ' <img src="'.$_base_path.'images/checkbox_check.gif" alt="'._AT('checked').'" title="'._AT('checked').'" height="13" width="13" style="vertical-align: middle" /> ';
	}

	// mark this choice
	if (isset($correct_choice)) {
		if ($checked && $correct_choice) {
			echo $right;
		} else if ($checked && !$correct_choice) {
			echo $wrong;
		} else if (!$checked && $correct_choice) {
			echo $wrong;
		} else { // !$checked && !$correct_choice
			echo $empty;
		}
	} else {
		echo $empty;
	}
	
	if ($checked) {
		echo $check_check;
	} else {
		echo $check_empty;
	}
	echo $text;
	if ($correct_choice == 1) {
		echo ' - ' . $right;
	}
}

function print_score($correct, $weight, $qid, $score, $put_zero = true, $disabled = false) {
	echo '<input type="text" size="5" name="scores['.$qid.']" value="';

	if ($score != '') {
		echo $score;
	} else if ($correct) {
		echo $weight;
	} else if ($put_zero) {
		echo '0';
	}
	echo '" style="width: 30px; font-weight: bold; text-align: right;" maxlength="4" '.($disabled ? 'disabled="disabled" ' : '').'/><strong> / '.floatval($weight).'</strong>';
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
	//possibley add a <noscript> link to filemanager with target="_blank"
}

function get_random_outof($test_id, $result_id) {	
	global $db;
	$total = 0;

	$sql	= 'SELECT SUM(Q.weight) AS weight FROM '.TABLE_PREFIX.'tests_questions_assoc Q, '.TABLE_PREFIX.'tests_answers A WHERE Q.test_id='.$test_id.' AND Q.question_id=A.question_id AND A.result_id='.$result_id;

	$result	= mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		return $row['weight'];
	}

	return 0;
}

?>