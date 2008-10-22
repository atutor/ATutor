<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

// if a valid user, then can come from the DB, otherwise
// this might come from _POST or even _SESSION
function get_test_result_id($test_id, &$max_pos) {
	global $db;

	if ($_SESSION['member_id']) {
		$sql	= "SELECT result_id, max_pos FROM ".TABLE_PREFIX."tests_results WHERE test_id=$test_id AND member_id='{$_SESSION['member_id']}' AND status=0";
	} else if ($_SESSION['test_result_id']) {
		// guest with on-going test
		$sql	= "SELECT result_id, max_pos FROM ".TABLE_PREFIX."tests_results WHERE test_id=$test_id AND result_id={$_SESSION['test_result_id']} AND status=0";
	} else {
		return 0; // new guest
	}

	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$max_pos = $row['max_pos'];
		return $row['result_id'];
	}

	return 0;
}

function init_test_result_questions($test_id, $is_random, $num_questions, $mid) {
	global $db;

	$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (NULL, $test_id, '".$mid."', NOW(), '', 0, NOW(), 0)";
	$result = mysql_query($sql, $db);
	$result_id = mysql_insert_id($db);

	if ($is_random) {
		// Retrieve 'num_questions' question_id randomly from those who are related to this test_id

		$non_required_questions = array();
		$required_questions     = array();

		$sql    = "SELECT question_id, required FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$test_id";
		$result	= mysql_query($sql, $db);
	
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['required'] == 1) {
				$required_questions[] = $row['question_id'];
			} else {
				$non_required_questions[] = $row['question_id'];
			}
		}
	
		$num_required = count($required_questions);
		if ($num_required < max(1, $num_questions)) {
			shuffle($non_required_questions);
			$required_questions = array_merge($required_questions, array_slice($non_required_questions, 0, $num_questions - $num_required));
		}

		$random_id_string = implode(',', $required_questions);

		$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id={$_SESSION['course_id']} AND TQA.test_id=$test_id AND TQA.question_id IN ($random_id_string) ORDER BY TQ.question_id";
	} else {
		$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id={$_SESSION['course_id']} AND TQA.test_id=$test_id ORDER BY TQA.ordering, TQA.question_id";
	}

	// $sql either gets a random set of questions (if $test_row['random']) ordered by 'question_id'
	// or the set of all questions for this test (sorted by 'ordering').
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_answers VALUES ($result_id, {$row['question_id']}, {$_SESSION['member_id']}, '', '', '')";
		mysql_query($sql, $db);
	}

	return $result_id;
}

// $num_questions must be greater than or equal to $row_required['cnt'] + $row_optional['cnt']
function get_total_weight($tid, $num_questions = null) {
	global $db;
    $sql = "SELECT SUM(weight) AS weight, COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid AND required = '1' GROUP BY required";
    $result = mysql_query($sql, $db);
    $row_required = mysql_fetch_assoc($result);

    $sql = "SELECT SUM(weight) AS weight, COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid AND required = '0' GROUP BY required";
    $result = mysql_query($sql, $db);
	$row_optional = mysql_fetch_assoc($result);
	
	$total_weight = 0;

	if ($num_questions == null) {
		$total_weight = $row_required['weight'] + $row_optional['weight'];
	} else if ($row_optional['cnt'] > 0) {
		$total_weight = $row_required['weight'] + ($row_optional['weight'] / $row_optional['cnt']) * min($num_questions - $row_required['cnt'], $row_optional['cnt']);
	}

	return $total_weight;
}

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

function print_question_cats($cat_id = 0) {	

	global $db;

	echo '<option value="0"';
	if ($cat_id == 0) {
		echo ' selected="selected"';
	}
	echo '>'._AT('cats_uncategorized').'</option>' . "\n";

	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'tests_questions_categories WHERE course_id='.$_SESSION['course_id'].' ORDER BY title';
	$result	= mysql_query($sql, $db);

	while ($row = mysql_fetch_array($result)) {
		echo '<option value="'.$row['category_id'].'"';
		if ($row['category_id'] == $cat_id) {
			echo ' selected="selected"';
		}
		echo '>'.$row['title'].'</option>\n';
	}
}

function print_VE ($area) {
?>
	<script type="text/javascript" language="javascript">
		document.writeln('<a href="#" onclick="javascript:window.open(\'<?php echo AT_BASE_HREF; ?>tools/tests/form_editor.php?area=<?php echo $area; ?>\',\'newWin1\',\'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480\'); return false;" style="cursor: pointer; text-decoration: none" ><?php echo _AT('use_visual_editor'); ?></a>');
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

// return the next guest id
function get_next_guest_id()
{
	global $db;
	
	$sql = "SELECT max(cast(substring(guest_id,3) as unsigned))+1 next_guest_id FROM ".TABLE_PREFIX."guests";
	$result	= mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (!is_int($row["next_guest_id"]))  // first guest id
		return "G_0";
	else
		return "G_". $row["next_guest_id"];
}
?>