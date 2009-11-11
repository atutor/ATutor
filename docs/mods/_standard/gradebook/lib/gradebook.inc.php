<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: gradebook.inc.php 7208 2008-05-28 16:07:24Z cindy $

// input: member_id
// return: array of grade scales created by member_id, member_id is set to 0 by default, which is to get preset grade scales
// returned array format:
//Array
//(
//    [1] => A+ - E
//    [2] => Pass - Fail
//    [3] => Excellent - Inadequate
//    [grade_scale_id] => scale_value_max - scale_value_min     (Value Explanation)
//)
define ('USE_HIGHER_GRADE', 1);
define ('USE_LOWER_GRADE', 2);
define ('NOT_OVERWRITE', 3);
define ('OVERWRITE', 4);

function get_grade_scales_array($member_id = 0)
{
	global $db;
	
	$sql = "SELECT d.grade_scale_id, MIN(percentage_to) min, MAX(percentage_to) max FROM ".TABLE_PREFIX."grade_scales_detail d, ".TABLE_PREFIX."grade_scales g WHERE d.grade_scale_id = g.grade_scale_id AND g.member_id = ". $member_id ." GROUP BY d.grade_scale_id";
	$result = mysql_query($sql, $db) or die(mysql_error());

	$preset_grade_scales = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$sql_min = "SELECT scale_value FROM ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id=".$row["grade_scale_id"]." AND percentage_to=".$row["min"];
		$result_min = mysql_query($sql_min, $db) or die(mysql_error());
		$row_min = mysql_fetch_assoc($result_min);
		$min_value = $row_min['scale_value'];
		
		$sql_max = "SELECT scale_value FROM ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id=".$row["grade_scale_id"]." AND percentage_to=".$row["max"];
		$result_max = mysql_query($sql_max, $db) or die(mysql_error());
		$row_max = mysql_fetch_assoc($result_max);
		$max_value = $row_max['scale_value'];
		
		$preset_grade_scales[$row["grade_scale_id"]] = $max_value . " - ". $min_value;
	}
	
	return $preset_grade_scales;
}

// generate html of dropdown list box on preset grade scales and grade scales created by current member Id
// parameter: $selected_grade_scale_id: the grade_scale_id that need to set to be selected.
// return: html text
function print_grade_scale_selectbox($selected_grade_scale_id = 0, $id_name="selected_grade_scale_id")
{
?>
		<select name="selected_grade_scale_id" id="<?php echo $id_name; ?>">
			<option value="0" <?php if ($selected_grade_scale_id  == 0) { echo 'selected="selected"'; } ?>><?php echo _AT('none'); ?></option>
		<?php
			// preset grade scales
			$preset_scales = get_grade_scales_array();

			if (count($preset_scales) > 0)
			{
		?>
			<optgroup label="<?php echo _AT('presets'); ?>">
		<?php
				//presets
				foreach ($preset_scales as $id=>$preset)
				{
					echo '<option value="'.$id.'" ';
					if ($selected_grade_scale_id  == $id) echo 'selected="selected"';
					echo '>'.$preset.'</option>'."\n\r";
				}
				echo '			</optgroup>'."\n\r";
			}

			//previously used
			$custom_scales = get_grade_scales_array($_SESSION["member_id"]);
			
			if (count($custom_scales) > 0) 
			{
				echo '			<optgroup label="'. _AT('custom').'">'."\n\r";
				foreach ($custom_scales as $id=>$custom) 
				{
					echo '<option value="'.$id.'" ';
					if ($selected_grade_scale_id  == $id) echo 'selected="selected"';
					echo '>'.$custom.'</option>'."\n\r";
				}
				echo '			</optgroup>'."\n\r";
			}
		?>
		</select>

<?php
}

// This function returns grade based on grade scale
// Note: $score can be one of: grade itself, percentage, raw final score. 
// If $score is raw final score, $out_of has to be provided, otherwise,
// don't have to provide $out_of
function get_mark_by_grade($grade_scale_id, $score, $out_of='')
{
	global $db;
	
	$score = trim($score);
	$out_of = trim($out_of);
	
	if ($out_of == '') $default_mark = $score;
	else $default_mark = $score ." / " . $out_of;

	// if $grade_scale_id is 0 or not given, return $score itself.
	if ($grade_scale_id == 0 || $grade_scale_id == '')
		$mark = $default_mark;
	else // raw score
	{
		$sql_grade = "SELECT * from ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id = ". $grade_scale_id. " ORDER BY percentage_to DESC";
		$result_grade	= mysql_query($sql_grade, $db) or die(mysql_error());
		
		if (mysql_num_rows($result_grade) == 0)
			$mark = $default_mark;
		else
		{
			// check if $score is already the grade. If it is, return $score
			while ($row_grade = mysql_fetch_assoc($result_grade))
			{
				if ($row_grade['scale_value'] == $score) return $score;
			}
			
			if (substr($score, -1) == '%') // percentage
				$mark_in_percentage = substr($score, 0, -1);
			else if ($out_of <> '' && $out_of <> 0)  // raw final score
				$mark_in_percentage = $score / $out_of * 100;

			mysql_data_seek($result_grade, 0);
			while ($row_grade = mysql_fetch_assoc($result_grade))
			{
				if ($mark_in_percentage <= $row_grade['percentage_to'] && $mark_in_percentage >= $row_grade['percentage_from'])
					$mark = $row_grade['scale_value'];
			}
		}
	}

	// in case grade definition does not cover all scores
	if ($mark == '') $mark = $default_mark;
	
	return $mark;
}

function get_member_grade($test_id, $member_id, $grade_scale_id)
{
	global $db;

	require_once(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
	
	$grade = "";
	
	// find out final_score, out_of
	$sql = "SELECT t.random, t.out_of, r.result_id, r.final_score FROM ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."tests_results r WHERE t.test_id=".$test_id." AND t.test_id=r.test_id AND r.member_id='".$member_id."'";
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	if (mysql_num_rows($result) > 0 && $row["final_score"] <> "")
	{
		if ($row['random']) {
			$out_of = get_random_outof($test_id, $row['result_id']);
		} else {
			$out_of = $row['out_of'];
		}
		$grade = get_mark_by_grade($grade_scale_id, $row["final_score"], $out_of);
	}
	
	return $grade;
}

// Return array of students in the given course who take the given test more than once
// Parameter: $member_id, $test_id
// Return: an empty array or 
//Array
//(
//    [member_id1] => [num_takes1]
//    [member_id2] => [num_takes2]
//    ...
//)
function get_studs_take_more_than_once($course_id, $test_id)
{
	global $db;
	
	$rtn_array = array();
	
	$sql = "SELECT m.member_id, count(result_id) num FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e, ".TABLE_PREFIX."tests_results t WHERE m.member_id = e.member_id AND e.course_id = ".$course_id." AND e.approved='y' AND e.role <> 'Instructor' AND e.member_id=t.member_id AND t.test_id=".$test_id." GROUP BY m.first_name, m.last_name having count(*) > 1";
	$result = mysql_query($sql, $db) or die(mysql_error());
	
	while ($row = mysql_fetch_assoc($result))
		$rtn_array[$row["member_id"]. " " . $row["last_name"]] = $row["num"];

	return $rtn_array;
}

// compare grades
// parameters: 2 grades to compare, grade_scale_id, "higher"/"lower": return higher or lower grade
//             grade can be percentage like 70% or grade defined in grade_scale_id, like "A", "B"...
// return: higher or lower grade depending on 4th parameter
//         or, -1 if grades are comparable
function compare_grades($grade1, $grade2, $gradebook_test_id, $mode = "higher")
{
	global $db;
	
	// get grade scale id
	$sql = "SELECT grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id = ".$gradebook_test_id;
	$result	= mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$grade_scale_id = $row["grade_scale_id"];
	
	if ($grade_scale_id == 0) // compare raw scores
	{
		// retrieve raw score
		$grade1 = trim(str_replace("%", "", $grade1));
		$grade2 = trim(str_replace("%", "", $grade2));
		
		if ($grade1 > $grade2) return 1;
		else if ($grade1 < $grade2) return -1;
		else return 0;
	}
	else
	{
		$grade1 = get_mark_by_grade($grade_scale_id, $grade1);
		$grade2 = get_mark_by_grade($grade_scale_id, $grade2);

		$grades = array();
	
		$sql_grade = "SELECT scale_value from ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id = ". $grade_scale_id. " ORDER BY percentage_to DESC";
		$result_grade	= mysql_query($sql_grade, $db) or die(mysql_error());
		while ($row_grade = mysql_fetch_assoc($result_grade))
		{
			$grades[] = $row_grade["scale_value"];
		}

		if (!in_array($grade1, $grades) || !in_array($grade2, $grades))
			return -1; // uncomparable
		else
		{
			$grade1_key = array_search($grade1, $grades);
			$grade2_key = array_search($grade2, $grades);

			if ($grade1_key > $grade2_key)
			{
				$higher_grade = $grade2;
				$lower_grade = $grade1;
			}
			else if ($grade1_key < $grade2_key)
			{
				$higher_grade = $grade1;
				$lower_grade = $grade2;
			}
			else $higher_grade = $lower_grade = $grade1;
		}
	}
	
	if ($mode == "higher") return $higher_grade;
	else return $lower_grade;
}

// check imported students and grades:
// 1. if the student exists in the class, if not, report error
// 2. if the grade already exists, if it is, report conflict
// parameter: an array of student/grade info
// Array
//(
//    [member_id] => 1
//    [fname] => angelo  (could be empty if [member_id] is given)
//    [lname] => yuan    (could be empty if [member_id] is given)
//    [email] => angelo@hotmail.com   (could be empty if [member_id] is given)
//    [grade] => 70%
//    [gradebook_test_id] => 4
//    [solve_conflict] => 0
//)
// return: an array of processed student/grade/error info
// Array
//(
//    [member_id] => 1
//    [fname] => angelo  (could be empty if [member_id] is given)
//    [lname] => yuan    (could be empty if [member_id] is given)
//    [email] => angelo@hotmail.com  (could be empty if [member_id] is given)
//    [grade] => 70%
//    [gradebook_test_id] => 4
//    [solve_conflict] => 0
//    [error] => "Student not exists"
//    [has_conflict] => 1
//)
function check_user_info($record)
{
	global $db;

	$record['fname'] = htmlspecialchars(stripslashes(trim($record['fname'])));
	$record['lname'] = htmlspecialchars(stripslashes(trim($record['lname'])));
	$record['member_id'] = htmlspecialchars(stripslashes(trim($record['member_id'])));
	$record['email'] = htmlspecialchars(stripslashes(trim($record['email'])));
	$record['grade'] = htmlspecialchars(stripslashes(trim($record['grade'])));

	if (empty($record['remove'])) {
		$record['remove'] = FALSE;			
	}

	if ($record['member_id'] == '')
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.first_name='".$record['fname']."' AND m.last_name='".$record['lname']."' AND m.email='".$record['email']."' AND m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role<>'Instructor'";
		$result = mysql_query($sql, $db) or die(mysql_error());
		
		if (mysql_num_rows($result) == 0) 
			$record['error'] = _AT("student_not_exists");
		else
		{
			$row = mysql_fetch_assoc($result);
			$record['member_id'] = $row["member_id"];
		}
	}
	
	if ($record['error'] == "" && $record['member_id'] > 0)
	{
		$sql = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$record['gradebook_test_id']. " AND member_id=".$record["member_id"];
		$result = mysql_query($sql, $db) or die(mysql_error());
		
		if (mysql_num_rows($result) > 0 && $record['solve_conflict'] == 0) 
		{
			$row = mysql_fetch_assoc($result);
			$record['error'] = _AT("grade_already_exists", $row["grade"]);
			$record['conflict'] = 1;
		}
		
		if (mysql_num_rows($result) > 0 && $record['solve_conflict'] > 0) 
		{
			$row = mysql_fetch_assoc($result);
			
			if ($record['solve_conflict'] == USE_HIGHER_GRADE || $record['solve_conflict'] == USE_LOWER_GRADE) 
			{
				if ($record['solve_conflict'] == USE_HIGHER_GRADE)
					$grade = compare_grades($record['grade'], $row['grade'], $record['gradebook_test_id'], "higher");

				if ($record['solve_conflict'] == USE_LOWER_GRADE)
					$grade = compare_grades($record['grade'], $row['grade'], $record['gradebook_test_id'], "lower");
				
				if ($grade == -1)
				{
					$record["error"] = _AT("grades_uncomparable");
					$record['conflict'] = 1;
				}
				else
					$record['grade'] = $grade;
			}
			
			if ($record['solve_conflict'] == NOT_OVERWRITE) $record['grade'] = $row['grade'];
			if ($record['solve_conflict'] == OVERWRITE) $record['grade'] = $record['grade'];
		}
	}
	
	if ($record['remove']) {
		//unset errors 
		$record['error'] = '';
	}
	
	return $record;
}

// update gradebook
function update_gradebook_external_test($students, $gradebook_test_id)
{
	global $db, $msg;

	foreach($students as $student)
	{
		if (!$student['remove'])
		{
			// retrieve member id
			$sql = "SELECT member_id FROM ".TABLE_PREFIX."members m WHERE m.first_name='".$student['fname']."' AND m.last_name='".$student['lname']."' AND m.email='".$student['email']."'";
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);
			$member_id = $row["member_id"];

			// retrieve grade scale id
			$sql = "SELECT grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id=".$gradebook_test_id;
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);
			$grade_scale_id = $row["grade_scale_id"];
			
			$grade = get_mark_by_grade($grade_scale_id, $student["grade"]);
			$sql = "REPLACE INTO ".TABLE_PREFIX."gradebook_detail(gradebook_test_id, member_id, grade) VALUES(".$gradebook_test_id.", ".$member_id.", '".$grade."')";
			$result = mysql_query($sql, $db) or die(mysql_error());
			
			$updated_list .= '<li>' . $student['fname'] . ', '. $student['lname']. ': '. $grade. '</li>';
		}
	}

	if ($updated_list) 
	{
	$feedback = array('GRADEBOOK_UPDATED', $updated_list);
	$msg->addFeedback($feedback);
	}
}

// return median value of the given array
function median($grade_array)
{
	$oe_value = count($grade_array); 
	
	if ($oe_value % 2 == 0 ) 
		$position = 1; 
	else 
		$position = 2; 
	
	if ($position == 2 ) 
		$median = $grade_array[(count($grade_array)/2)]; 
	else 
		$median= $grade_array[(count($grade_array)/2)-1]; 
	
	return $median;
}

// return class average of the given test_id
function get_class_avg($gradebook_test_id)
{
	global $db;
	
	$sql = "SELECT * FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id=".$gradebook_test_id;
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	if ($row["id"]<>0)  // internal atutor test
	{
		require_once (AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

		$sql_test = "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=".$row["id"];
		$result_test = mysql_query($sql_test, $db) or die(mysql_error());
		$row_test = mysql_fetch_assoc($result_test);

		if ($row_test['out_of'] == 0 || $row_test['result_release']==AT_RELEASE_NEVER)
			return _AT("na");
		
		$sql_marks = "SELECT * FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$row["id"]. " AND status=1";
		$result_marks = mysql_query($sql_marks, $db) or die(mysql_error());
		
		$num_students = 0;
		$total_final_score = 0;
		$total_out_of = 0;
		while ($row_marks = mysql_fetch_assoc($result_marks))
		{
			if ($row_marks['final_score'] == '' ) continue;
			
			$num_students++;
			$total_final_score += $row_marks["final_score"];

			if ($row_test['random'])
				$total_out_of += get_random_outof($row_marks['test_id'], $row_marks['result_id']);
			else
				$total_out_of += $row_test['out_of'];
		}
		
		if ($num_students > 0)
		{
			$avg_final_score = round($total_final_score / $num_students);
			$avg_out_of = round($total_out_of / $num_students);
		}
		
		if ($avg_final_score <> "") 
			$avg_grade = get_mark_by_grade($row["grade_scale_id"], $avg_final_score, $avg_out_of);
		else
			$avg_grade = "";
	}
	else  // external test
	{
		$sql_grades = "SELECT * FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$gradebook_test_id." ORDER BY grade";
		$result_grades = mysql_query($sql_grades, $db) or die(mysql_error());
		
		$grade_array = array();
		while ($row_grades = mysql_fetch_assoc($result_grades))
			$grade_array[] = $row_grades["grade"];
			
		$avg_grade = median($grade_array);
	}
	
	if ($avg_grade == "") return _AT("na");
	else return $avg_grade;
}
?>
