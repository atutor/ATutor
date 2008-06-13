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
// $Id: grade_scale_add.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

require_once(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
require_once("lib/gradebook.inc.php");

// Checks if the given test has students taken it more than once, if has,
// print feedback and return false, otherwise, return true.
function is_test_updatable($test_id)
{
	global $db, $msg;
	
	$no_error = true;
	
	$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $test_id);
	
	foreach ($studs_take_num as $student => $num)
	{
			if ($no_error) $no_error = false;
			
			$f = array('UPDATE_GRADEBOOK',
							$row['title'], 
							$student . ": " . $num . " times");
			$msg->addFeedback($f);
	}
	
	if ($no_error) 
		return true;
	else 
		return false;
}

function update_gradebook($test_id, $member_id)
{
	global $db;

	$sql = "SELECT gradebook_test_id, grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE test_id = ". $test_id;
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$gradebook_test_id = $row["gradebook_test_id"];
	$grade_scale_id = $row["grade_scale_id"];
	
	// find out final_score, out_of
	$sql = "SELECT t.random, t.out_of, r.result_id, r.final_score FROM ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."tests_results r WHERE t.test_id=".$test_id." AND t.test_id=r.test_id AND r.member_id=".$member_id;
	$result = mysql_query($sql, $db) or die(mysql_error());

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		if ($row['random']) {
			$out_of = get_random_outof($test_id, $row['result_id']);
		} else {
			$out_of = $row['out_of'];
		}
		
		$sql = "REPLACE INTO ".TABLE_PREFIX."gradebook_detail(gradebook_test_id, member_id, grade) VALUES(".$gradebook_test_id.", ".$member_id.", '".get_mark_by_grade($grade_scale_id, $row["final_score"], $out_of)."')";
		$result = mysql_query($sql, $db) or die(mysql_error());
	}
}

// Initialize all applicable tests array and all enrolled students array
$tests = array();
$students = array();

// generate test array
$sql = "SELECT *, t.title FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t WHERE g.test_id = t.test_id AND t.course_id=".$_SESSION["course_id"];
$result	= mysql_query($sql, $db) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
{
	$test["test_id"] =  $row["test_id"];
	$test["title"] =  $row["title"];
	
	array_push($tests, $test);
}

// generate students array
$sql = "SELECT m.first_name, m.last_name, e.member_id FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role<>'Instructor' ORDER BY m.first_name,m.last_name";
$result	= mysql_query($sql, $db) or die(mysql_error());

while ($row = mysql_fetch_assoc($result))
{
	$student["first_name"] = $row["first_name"];
	$student["last_name"] = $row["last_name"];
	$student["member_id"] = $row["member_id"];
	
	array_push($students, $student);
}
// end of initialization

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['update'])) 
{
	if ($_POST['test_id'] == -1 || $_POST['member_id'] == -1)
	{
		$msg->addError('NO_ITEM_SELECTED');
	}

	if (!$msg->containsErrors()) 
	{
		if ($_POST["test_id"] == 0)
		{
			foreach($tests as $test)
			{
				if (is_test_updatable($test["test_id"]))
				{
					if ($_POST["member_id"]==0)
					{
						// delete old data for this test
						$sql = "DELETE from ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id = (SELECT gradebook_test_id FROM ".TABLE_PREFIX."gradebook_tests WHERE test_id=".$test["test_id"].")";
						$result	= mysql_query($sql, $db) or die(mysql_error());
						
						foreach($students as $student)
							update_gradebook($test["test_id"], $student["member_id"]);
					}
					else
						update_gradebook($test["test_id"], $_POST["member_id"]);
				}
			}
		}
		else
		{
			if (is_test_updatable($_POST["test_id"]))
			{
				if ($_POST["member_id"]==0)
				{
					// delete old data for this test
					$sql = "DELETE from ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id = (SELECT gradebook_test_id FROM ".TABLE_PREFIX."gradebook_tests WHERE test_id=".$_POST["test_id"].")";
					$result	= mysql_query($sql, $db) or die(mysql_error());
					
					foreach($students as $student)
						update_gradebook($_POST["test_id"], $student["member_id"]);
				}
				else
					update_gradebook($_POST["test_id"], $_POST["member_id"]);
			}
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('update_gradebook'); ?></legend>

<?php
if (count($tests) == 0)
{
?>
	<div class="row">
		<strong><?php echo _AT('none_found'); ?></strong>
	</div>
<?php 
}
else
{
	// list of tests
	echo '	<div class="row">'."\n\r";
	echo '		<label for="select_tid">'. _AT("name") .'</label><br />'."\n\r";
	echo '		<select name="test_id" id="select_tid">'."\n\r";
	echo '			<option value="0">'. _AT('all_atutor_tests') .'</option>'."\n\r";
	echo '			<option value="-1"></option>."\n\r"';

	foreach($tests as $test)
	{
		echo '			<option value="'.$test[test_id].'">'.$test[title].'</option>'."\n\r";
	}
	echo '		</select>'."\n\r";
	echo '	</div>'."\n\r";

	// list of students
	echo '	<div class="row">'."\n\r";
	echo '		<label for="select_mid">'. _AT("students") .'</label><br />'."\n\r";
	echo '		<select name="member_id" id="select_sid">'."\n\r";
	echo '			<option value="0">'. _AT('all_students') .'</option>'."\n\r";
	echo '			<option value="-1"></option>."\n\r"';

	foreach($students as $student)
	{
		echo '			<option value="'.$student[member_id].'">'.$student[first_name].' '.$student[last_name].'</option>'."\n\r";
	}
	echo '		</select>'."\n\r";
	echo '	</div>'."\n\r";
?>

	<div class="row buttons">
		<input type="submit" name="update" value="<?php echo _AT('update'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
<?php
}
?>
	</fieldset>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
