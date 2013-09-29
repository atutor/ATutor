<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

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

	$sql = "SELECT d.grade_scale_id, MIN(percentage_to) min, MAX(percentage_to) max FROM ".TABLE_PREFIX."grade_scales_detail d, ".TABLE_PREFIX."grade_scales g WHERE d.grade_scale_id = g.grade_scale_id AND g.member_id = ". $member_id ." GROUP BY d.grade_scale_id";
	$rows_scales = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $member_id));

	$preset_grade_scales = array();
	foreach($rows_scales as $row){

		$sql_min = "SELECT scale_value FROM %sgrade_scales_detail WHERE grade_scale_id=%d AND percentage_to=%d";
		$row_min = queryDB($sql_min, array(TABLE_PREFIX, $row['grade_scale_id'], $row['min']), TRUE);
		$min_value = $row_min['scale_value'];

		$sql_max = "SELECT scale_value FROM %sgrade_scales_detail WHERE grade_scale_id=%d AND percentage_to=%d";
		$row_max = queryDB($sql_max, array(TABLE_PREFIX, $row['grade_scale_id'], $row['max']), TRUE);
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

	$score = trim($score);
	$out_of = trim($out_of);

	if ($out_of == '') $default_mark = $score;
	else $default_mark = $score ." / " . $out_of;

	// if $grade_scale_id is 0 or not given, return $score itself.
	if ($grade_scale_id == 0 || $grade_scale_id == '')
		$mark = $default_mark;
	else // raw score
	{
		$sql_grade = "SELECT * from %sgrade_scales_detail WHERE grade_scale_id = %d ORDER BY percentage_to DESC";
		$rows_scales	= queryDB($sql_grade, array(TABLE_PREFIX, $grade_scale_id));
		
		if(count($rows_scales) == 0)
			$mark = $default_mark;
		else
		{
			// check if $score is already the grade. If it is, return $score
			foreach($rows_scales as $row_grade){
				if ($row_grade['scale_value'] == $score) return $score;
			}
			
			if (substr($score, -1) == '%') // percentage
				$mark_in_percentage = substr($score, 0, -1);
			else if ($out_of <> '' && $out_of <> 0)  // raw final score
				$mark_in_percentage = $score / $out_of * 100;

			foreach($rows_scales as $row_grade){
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
    // THIS FUNCTION UNTESTED WITH queryDB()
    
	require_once(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');
	
	$grade = "";
	
	// find out final_score, out_of

	$sql = "SELECT t.random, t.out_of, r.result_id, r.final_score FROM %stests t, %stests_results r WHERE t.test_id=%d AND t.test_id=r.test_id AND r.member_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $test_id, $member_id), TRUE);

    if(count($row) > 0 && $row["final_score"] <> ""){

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
	// THIS FUNCTION UNTESTED WITH queryDB()
	
	$rtn_array = array();

	$sql = "SELECT m.member_id, count(result_id) num FROM %smembers m, %scourse_enrollment e, %stests_results t WHERE m.member_id = e.member_id AND e.course_id = %d AND e.approved='y' AND e.role <> 'Instructor' AND e.member_id=t.member_id AND t.test_id=%d GROUP BY m.first_name, m.last_name having count(*) > 1";
	$rows_members = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $course_id, $test_id));

    foreach($rows_members as $row){
		$rtn_array[$row["member_id"]. " " . $row["last_name"]] = $row["num"];
    }
	return $rtn_array;
}

// compare grades
// parameters: 2 grades to compare, grade_scale_id, "higher"/"lower": return higher or lower grade
//             grade can be percentage like 70% or grade defined in grade_scale_id, like "A", "B"...
// return: higher or lower grade depending on 4th parameter
//         or, -1 if grades are comparable
function compare_grades($grade1, $grade2, $gradebook_test_id, $mode = "higher")
{

	$sql = "SELECT grade_scale_id FROM %sgradebook_tests WHERE gradebook_test_id = %d";
	$row	= queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id), TRUE);
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

		$sql_grade = "SELECT scale_value from %sgrade_scales_detail WHERE grade_scale_id = %d ORDER BY percentage_to DESC";
		$rows_grade	= queryDB($sql_grade, array(TABLE_PREFIX, $grade_scale_id));
		
		foreach($rows_grade as $row_grade){
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

		$sql = "SELECT * FROM %smembers m, %scourse_enrollment e WHERE m.first_name='%s' AND m.last_name='%s' AND m.email='%s' AND m.member_id = e.member_id AND e.course_id=%d AND e.approved='y' AND e.role<>'Instructor'";
		$row = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $record['fname'], $record['lname'], $record['email'], $_SESSION["course_id"]), TRUE);
		
		if(count($row) == 0){	
			$record['error'] = _AT("student_not_exists");
		}else{

			$record['member_id'] = $row["member_id"];
		}
	}
	
	if ($record['error'] == "" && $record['member_id'] > 0)
	{

		$sql = "SELECT grade FROM %sgradebook_detail WHERE gradebook_test_id=%d AND member_id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $record['gradebook_test_id'], $record["member_id"]), TRUE);
				
		if (count($row) > 0 && $record['solve_conflict'] == 0){

			$record['error'] = _AT("grade_already_exists", $row["grade"]);
			$record['conflict'] = 1;
		}
		
		if (count($row) > 0 && $record['solve_conflict'] > 0) 
		{
			
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
	global  $msg;

	foreach($students as $student)
	{
		if (!$student['remove'])
		{
			// retrieve member id

			$sql = "SELECT member_id FROM %smembers m WHERE m.first_name='%s' AND m.last_name='%s' AND m.email='%s'";
			$row = queryDB($sql, array(TABLE_PREFIX, $student['fname'], $student['lname'], $student['email']), TRUE);

			$member_id = $row["member_id"];

			// retrieve grade scale id

			$sql = "SELECT grade_scale_id FROM %sgradebook_tests WHERE gradebook_test_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id), TRUE);

			$grade_scale_id = $row["grade_scale_id"];
			
			$grade = get_mark_by_grade($grade_scale_id, $student["grade"]);

			$sql = "REPLACE INTO %sgradebook_detail(gradebook_test_id, member_id, grade) VALUES(%d, %d, '%s')";
			$result = queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id, $member_id, $grade));	
				
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

	$sql = "SELECT * FROM %sgradebook_tests WHERE gradebook_test_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $gradebook_test_id), TRUE);
		
	if ($row["id"]<>0)  // internal atutor test
	{
		require_once (AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

		$sql_test = "SELECT * FROM %stests WHERE test_id=%d";
		$row_test = queryDB($sql_test, array(TABLE_PREFIX, $row["id"]), TRUE);
		
		if ($row_test['out_of'] == 0 || $row_test['result_release']==AT_RELEASE_NEVER)
			return _AT("na");

		$sql_marks = "SELECT * FROM %stests_results WHERE test_id=%d AND status=1";
		$rows_marks = queryDB($sql_marks, array(TABLE_PREFIX, $row["id"]));
				
		$num_students = 0;
		$total_final_score = 0;
		$total_out_of = 0;
		foreach($rows_marks as $row_marks){

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

		$sql_grades = "SELECT * FROM %sgradebook_detail WHERE gradebook_test_id= %d ORDER BY grade";
		$rows_grades = queryDB($sql_grades, array(TABLE_PREFIX, $gradebook_test_id, ));
				
		$grade_array = array();
		foreach($rows_grades as $row_grades){
			$grade_array[] = $row_grades["grade"];
		}	
		$avg_grade = median($grade_array);
	}
	
	if ($avg_grade == "") return _AT("na");
	else return $avg_grade;
}
?>
