<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = "gradebook";

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);
tool_origin($_SERVER['HTTP_REFERER']);
// initialize relationship between gradebook_test_id and external test name
$tests = array();

// atutor tests
//$sql = "SELECT * FROM ".TABLE_PREFIX."gradebook_tests WHERE course_id=".$_SESSION["course_id"]." ORDER BY title";

$sql_at = "SELECT gradebook_test_id, t.title FROM %sgradebook_tests g, %stests t".
				" WHERE g.id=t.test_id " .
				" AND g.type='ATutor Test' ".
				" AND t.course_id=%d".
				" ORDER BY t.title";
$rows_at = queryDB($sql_at, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION["course_id"]));

// atutor assignments
$sql_aa = "SELECT gradebook_test_id, a.title FROM %sgradebook_tests g, %sassignments a".
				" WHERE g.id=a.assignment_id " .
				" AND g.type='ATutor Assignment' ".
				" AND a.course_id=%d".
				" ORDER BY a.title";
$rows_aa = queryDB($sql_aa, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION["course_id"]));

// external
$sql_e = "SELECT gradebook_test_id, title FROM %sgradebook_tests".
				" WHERE type='External' ".
				" AND course_id=%d".
				" ORDER BY title";
$rows_e = queryDB($sql_e, array(TABLE_PREFIX, $_SESSION["course_id"]));

// end of initialization

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	$return_url = $_SESSION['tool_origin']['url'];
    tool_origin('off');
	header('Location: '.$return_url);
	//header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['export'])) 
{
	// generate students array
	$sql = "SELECT m.first_name, m.last_name, m.email, e.member_id FROM %smembers m, %scourse_enrollment e WHERE m.member_id = e.member_id AND e.course_id=%d AND e.approved='y' AND e.role<>'Instructor' ORDER BY m.first_name,m.last_name,m.email";
	$rows_enrolled	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION["course_id"]));
		
	if(count($rows_enrolled) == 0){
		// nothing to send. empty file
		$msg->addError('ENROLLMENT_NONE_FOUND');
	}
	else
	{
		$this_row = "First Name, Last Name, Email, Grade\n";
		foreach($rows_enrolled as $row){

			// retrieve title

			$sql_title = "(SELECT g.gradebook_test_id, t.title".
							" FROM %sgradebook_tests g, %stests t".
							" WHERE g.type='ATutor Test'".
							" AND g.id = t.test_id".
							" AND g.gradebook_test_id=%d)".
							" UNION (SELECT g.gradebook_test_id, a.title".
							" FROM %sgradebook_tests g, %sassignments a".
							" WHERE g.type='ATutor Assignment'".
							" AND g.id = a.assignment_id".
							" AND g.gradebook_test_id=%d)".
							" UNION (SELECT gradebook_test_id, title ".
							" FROM %sgradebook_tests".
							" WHERE type='External'".
							" AND gradebook_test_id=%d)";
			$row_title	= queryDB($sql_title, array(TABLE_PREFIX, TABLE_PREFIX, $_POST['gradebook_test_id'], TABLE_PREFIX, TABLE_PREFIX, $_POST['gradebook_test_id'], TABLE_PREFIX, $_POST['gradebook_test_id']), TRUE);
		
			// retrieve grade
			$sql_grade = "SELECT grade FROM %sgradebook_detail WHERE gradebook_test_id=%d AND member_id=%d";
			$row_grade	= queryDB($sql_grade, array(TABLE_PREFIX, $_POST["gradebook_test_id"], $row["member_id"]), TRUE);
		
			$grade=$row_grade["grade"];
			
			$this_row .= quote_csv($row['first_name']).",";
			$this_row .= quote_csv($row['last_name']).",";
			$this_row .= quote_csv($row['email']).",";
			$this_row .= quote_csv($grade)."\n";
		}
		header('Content-Type: text/csv');
		header('Content-transfer-encoding: binary');
		header('Content-Disposition: attachment; filename="grade_'.$row_title["title"].'.csv"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		echo $this_row;
		exit;
	}
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
if(count($rows_aa) == 0 && count($rows_at) == 0 && count($rows_e) == 0){
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
    if(count($rows_aa) > 0){
		echo '			<optgroup label="'. _AT('assignments') .'">'."\n\r";
	    foreach($rows_aa as $row_aa){
			echo '			<option value="'.$row_aa['gradebook_test_id'].'">'.htmlspecialchars_decode(stripslashes($row_aa['title'])).'</option>'."\n\r";
		}
		echo '			</optgroup>'."\n\r";
	}
    if(count($rows_at) > 0){
		echo '			<optgroup label="'. _AT('tests') .'">'."\n\r";
	    foreach($rows_at as $row_at){
			echo '			<option value="'.$row_at['gradebook_test_id'].'">'.htmlspecialchars_decode(stripslashes($row_at['title'])).'</option>'."\n\r";
		}
		echo '			</optgroup>'."\n\r";
	}
    if(count($rows_e) > 0){
		echo '			<optgroup label="'. _AT('external_tests') .'">'."\n\r";
	   foreach($rows_e as $row_e){ 
			echo '			<option value="'.$row_e['gradebook_test_id'].'">'.htmlspecialchars_decode(stripslashes($row_e['title'])).'</option>'."\n\r";
		}
		echo '			</optgroup>'."\n\r";
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

<form name="form1" method="post" action="mods/_standard/gradebook/verify_list.php" enctype="multipart/form-data">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import'); ?></legend>
	<div class="row">
		<p><?php echo _AT('import_marks_info'); ?></p>
	</div>

<?php 
if(count($rows_aa) == 0 && count($rows_e) == 0){
?>
	<div class="row">
		<strong><?php echo _AT('none_found'); ?></strong>
	</div>
<?php 
}

?>
	<div class="row">
		<label for="select_gid2"><?php echo _AT('import_content_package_where'); ?></label><br />
		<select name="gradebook_test_id" id="select_gid2">
<?php

    if(count($rows_aa) > 0){
		echo '			<optgroup label="'. _AT('assignments') .'">'."\n\r";
	    
	    foreach($rows_aa as $row_aa){
			echo '			<option value="'.$row_aa['gradebook_test_id'].'">'.htmlspecialchars_decode(stripslashes($row_aa['title'])).'</option>'."\n\r";
		}
		echo '			</optgroup>'."\n\r";
	}
    
    if(count($rows_e) > 0){
		echo '			<optgroup label="'. _AT('external_tests') .'">'."\n\r";
	    
	    foreach($rows_e as $row_e){
			echo '			<option value="'.$row_e['gradebook_test_id'].'">'.htmlspecialchars_decode(stripslashes($row_e['title'])).'</option>'."\n\r";
		}
		echo '			</optgroup>'."\n\r";
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