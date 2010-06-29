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
// $Id: grade_scale_add.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

require_once("lib/gradebook.inc.php");

// Checks if the given test has students taken it more than once, if has,
// print feedback and return false, otherwise, return true.
function is_test_updatable($gradebook_test_id)
{
	global $db, $msg;
	
	$sql = "SELECT g.id, t.title FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t WHERE g.id=t.test_id AND g.type='ATutor Test' AND g.gradebook_test_id = ". $gradebook_test_id;
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	$no_error = true;
	
	$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $row["id"]);
	
	foreach ($studs_take_num as $member_id => $num)
	{
		if ($no_error) $no_error = false;
		$error_msg .= get_display_name($member_id) . ": " . $num . " times<br>";
	}
		
	if (!$no_error)
	{
		$f = array('UPDATE_GRADEBOOK',
						$row['title'], 
						$error_msg);
		$msg->addFeedback($f);
	}

	if ($no_error) 
		return true;
	else 
		return false;
}

function update_gradebook($gradebook_test_id, $member_id)
{
	global $db;

	$sql = "SELECT id, grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id = ". $gradebook_test_id;
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$test_id = $row["id"];
	$grade_scale_id = $row["grade_scale_id"];
	
	// get grade
	$grade = get_member_grade($test_id, $member_id, $grade_scale_id);
	
	if ($grade <> "")
	{
		$sql = "REPLACE INTO ".TABLE_PREFIX."gradebook_detail(gradebook_test_id, member_id, grade) VALUES (".$gradebook_test_id.", ".$member_id.", '".$grade."')";
		$result = mysql_query($sql, $db) or die(mysql_error());
	}
}

// Initialize all applicable tests array and all enrolled students array
$tests = array();
$students = array();

// generate gradebook test array
$sql = "SELECT *, t.title FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t WHERE g.id = t.test_id AND g.type='ATutor Test' AND t.course_id=".$_SESSION["course_id"];
$result	= mysql_query($sql, $db) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
{
	$test["gradebook_test_id"] =  $row["gradebook_test_id"];
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
	if (!$msg->containsErrors()) 
	{
		if ($_POST["gradebook_test_id"] == 0)
		{
			foreach($tests as $test)
			{
				if (is_test_updatable($test["gradebook_test_id"]))
				{
					if ($_POST["member_id"]==0)
					{
						// delete old data for this test
						$sql = "DELETE from ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id = ".$test["gradebook_test_id"];
						$result	= mysql_query($sql, $db) or die(mysql_error());
						
						foreach($students as $student)
							update_gradebook($test["gradebook_test_id"], $student["member_id"]);
					}
					else
						update_gradebook($test["gradebook_test_id"], $_POST["member_id"]);
				}
			}
		}
		else
		{
			if (is_test_updatable($_POST["gradebook_test_id"]))
			{
				if ($_POST["member_id"]==0)
				{
					// delete old data for this test
					$sql = "DELETE from ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id = ".$_POST["gradebook_test_id"];
					$result	= mysql_query($sql, $db) or die(mysql_error());
					
					foreach($students as $student)
						update_gradebook($_POST["gradebook_test_id"], $student["member_id"]);
				}
				else
					update_gradebook($_POST["gradebook_test_id"], $_POST["member_id"]);
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
	echo '		<label for="select_tid1">'. _AT("tests") .'</label><br />'."\n\r";
	echo '		<select name="gradebook_test_id" id="select_tid1">'."\n\r";
	echo '			<option value="0">'. _AT('all') .'</option>'."\n\r";

	foreach($tests as $test)
	{
		echo '			<option value="'.$test[gradebook_test_id].'">'.$test[title].'</option>'."\n\r";
	}
	echo '		</select>'."\n\r";
	echo '	</div>'."\n\r";

	// list of students
	echo '	<div class="row">'."\n\r";
	echo '		<label for="select_sid">'. _AT("students") .'</label><br />'."\n\r";
	echo '		<select name="member_id" id="select_sid">'."\n\r";
	echo '			<option value="0">'. _AT('all') .'</option>'."\n\r";

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

<form name="form1" method="post" action="mods/_standard/gradebook/verify_tests.php">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('combine_tests'); ?></legend>
	<div class="row">
		<p><?php echo _AT('combine_tests_info'); ?></p>
	</div>

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
	echo '		<label for="select_tid2">'. _AT("tests") .' '. _AT("combine_into").'</label><br />'."\n\r";
	echo '		<select name="gradebook_test_id" id="select_tid2">'."\n\r";

	foreach($tests as $test)
		echo '			<option value="'.$test[gradebook_test_id].'">'.$test[title].'</option>'."\n\r";

	echo '		</select>'."\n\r";
	echo '	</div>'."\n\r";

	// list of atutor tests that can be combined. 
	// These tests can only be taken once and are not in gradebook yet
	// note: surveys are excluded by checking if question weights are defined
	$sql_at = "SELECT * FROM ".TABLE_PREFIX."tests t".
					" WHERE course_id=".$_SESSION["course_id"].
					" AND num_takes = 1".
					" AND NOT EXISTS (SELECT 1".
													" FROM ".TABLE_PREFIX."gradebook_tests g".
													" WHERE g.id = t.test_id".
													" AND g.type='ATutor Test')".
				" AND test_id IN (SELECT test_id FROM ".TABLE_PREFIX."tests_questions_assoc ".
								" GROUP BY test_id ".
								" HAVING sum(weight) > 0) ".
					" ORDER BY title";
	$result_at = mysql_query($sql_at, $db) or die(mysql_error());
	
	if (mysql_num_rows($result_at) == 0)
		 echo _AT('none_found');
	else
	{
		echo '	<div class="row">'."\n\r";
		echo '		<label for="select_tid3">'. _AT("tests") .' '. _AT("combine_from").'</label><br />'."\n\r";
		echo '		<select name="test_id" id="select_tid3">'."\n\r";
		
		if (mysql_num_rows($result_at) > 0)
		{
			while ($row_at = mysql_fetch_assoc($result_at))
			{
				echo '			<option value="'.$row_at[test_id].'">'.$row_at[title].'</option>'."\n\r";
			}
		}
	
		echo '		</select>'."\n\r";
		echo '	</div>'."\n\r";
	}
?>

	<div class="row buttons">
		<input type="submit" name="combine" value="<?php echo _AT('combine'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
<?php
}
?>
	</fieldset>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
