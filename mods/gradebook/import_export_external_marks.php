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
// $Id: index.php 7482 2008-05-06 17:44:49Z greg $

$page = "gradebook";

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

// initialize relationship between gradebook_test_id and external test name
$tests = array();
$sql = "SELECT * FROM ".TABLE_PREFIX."gradebook_tests WHERE course_id=".$_SESSION["course_id"]." ORDER BY title";
$result = mysql_query($sql, $db) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$tests[$row["gradebook_test_id"]] = $row["title"];
// end of initialization

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['export'])) 
{
	// generate students array
	$sql = "SELECT m.first_name, m.last_name, m.email, e.member_id FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role<>'Instructor' ORDER BY m.first_name,m.last_name,m.email";
	$result	= mysql_query($sql, $db) or die(mysql_error());
	
	if (mysql_num_rows($result)==0)
	{
		// nothing to send. empty file
		$msg->addError('ENROLLMENT_NONE_FOUND');
	}
	else
	{
		$this_row = "First Name, Last Name, Email, Grade\n";
		while ($row = mysql_fetch_assoc($result))
		{
			$sql_grade = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$_POST["gradebook_test_id"]." AND member_id=".$row["member_id"];
			$result_grade	= mysql_query($sql_grade, $db) or die(mysql_error());
			$row_grade = mysql_fetch_assoc($result_grade);
			$grade=$row_grade["grade"];
			
			$this_row .= quote_csv($row['first_name']).",";
			$this_row .= quote_csv($row['last_name']).",";
			$this_row .= quote_csv($row['email']).",";
			$this_row .= quote_csv($grade)."\n";
		}
		header('Content-Type: text/csv');
		header('Content-transfer-encoding: binary');
		header('Content-Disposition: attachment; filename="grade_'.$tests[$_POST["gradebook_test_id"]].'.csv"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		echo $this_row;
		exit;
	}
}
else if (isset($_POST['import'])) 
{
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('export'); ?></legend>
	<div class="row">
		<p><?php echo _AT('export_marks_info'); ?></p>
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
?>
	<div class="row">
		<label for="select_gid"><?php echo _AT('export_content_package_what'); ?></label><br />
		<select name="gradebook_test_id" id="select_gid">
<?php
			foreach($tests as $gradebook_test_id => $title)
			{
				echo '			<option value="'.$gradebook_test_id.'">'.$title.'</option>'."\n\r";
			}
?>
</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
<?php 
}
?>
	</fieldset>
</div>
</form>

<br /><br />

<form name="form1" method="post" action="mods/gradebook/verify_list.php" enctype="multipart/form-data"">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import'); ?></legend>
	<div class="row">
		<p><?php echo _AT('import_marks_info'); ?></p>
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
?>
	<div class="row">
		<label for="select_gid2"><?php echo _AT('import_content_package_where'); ?></label><br />
		<select name="gradebook_test_id" id="select_gid2">
<?php
			foreach($tests as $gradebook_test_id => $title)
			{
				echo '			<option value="'.$gradebook_test_id.'">'.$title.'</option>'."\n\r";
			}
?>
		</select>
	</div>
	
	<div class="row">
		<label for="to_file"><?php echo _AT('upload'); ?></label><br />
		<input type="file" name="file" id="to_file" />
	</div>

	<div class="row buttons">
		<input type="submit" name="import" value="<?php echo _AT('import'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
<?php 
}
?>
	</fieldset>
</div>
</form>

<?php 

/**
* Creates csv file to be exported
* @access  private
* @param   string $line		The line ot be converted to csv
* @return  string			The line after conversion to csv
* @author  Shozub Qureshi
*/
function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}

require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>