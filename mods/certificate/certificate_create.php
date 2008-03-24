<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: certificate_create.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFICATE);

// Main process
// Initialize default vars
require_once("common.inc.php");
initialize_default_vars();

$is_passscore_in_basetable = is_pass_score_defined_in_base_table();

if (isset($_POST['submit']))
{
	$missing_fields = array();

	/* insert or update a category */
	if ($_POST["test_id"] == -1)	
		$missing_fields[] = _AT("choose_test");
		
	if ($is_passscore_in_basetable)
	{
		if ($_POST["test_id"]>0)
		{
			$sql = "SELECT passscore, passpercent from ".TABLE_PREFIX."tests where test_id=".$_POST["test_id"];
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row=mysql_fetch_assoc($result);
			
			if ($row["passpercent"]==0 && $row["passscore"]==0)
			{
				$missing_fields[] = _AT("pass_score"). "<br>". _AT("define_pass_score", $_base_href."tools/tests/edit_test.php?tid=".$_POST["test_id"]);
			}
		}
	}
	else if ((!isset($_POST["pass_score"]) || ($_POST["passpercent"] == 0 && $_POST["passscore"] == 0)))
	{
		$missing_fields[] = _AT("pass_score");
	}
		
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) 
	{
   	if (isset($_POST["passscore"])) 
   		$passscore = $_POST["passscore"];
   	else
   		$passscore = 0;
   	
   	if (isset($_POST["passpercent"])) 
   		$passpercent = $_POST["passpercent"];
   	else
   		$passpercent = 0;
   	
   	$sql = "INSERT INTO ".TABLE_PREFIX."certificate
   	        (test_id, 
   	         passscore,
   	         passpercent,
   	         organization,
   	         enable_download) 
		        VALUES (". $_POST["test_id"] .", 
		                ". $passscore .",
		                ". $passpercent .",
		                '". $_POST["organization"]."',
		                ". $_POST["enable_download"] .")";
		                
		$result = mysql_query($sql, $db) or die(mysql_error());
		$certificate_id = mysql_insert_id($db);
		write_to_log(AT_ADMIN_LOG_INSERT, 'certificate', mysql_affected_rows($db), $sql);

		for ($i = 0; $i < count($fields_array); $i++)
		{
	   	$sql = "INSERT INTO ".TABLE_PREFIX."certificate_text
	   	        (certificate_id, 
	   	         field_name,
	   	         field_value) 
			        VALUES (". $certificate_id .", 
			                '". $fields_array[$i]["FieldName"] ."',
			                '". $_POST["fields_value"][$i] ."')";
			                
			$result = mysql_query($sql, $db) or die(mysql_error());
			write_to_log(AT_ADMIN_LOG_INSERT, 'certificate_text', mysql_affected_rows($db), $sql);
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: index_instructor.php');
		exit;
	}
} 
else if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">
	<div class="row">
	<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="test_id"><?php echo _AT('choose_test'); ?></label><br>
		<select name="test_id" id="test_id">
			<option value="-1">- <?php echo _AT('select'); ?> -</option>
<?php
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] AND test_id not in (SELECT distinct test_id FROM ".TABLE_PREFIX."certificate) ORDER BY start_date DESC";
$result = mysql_query($sql, $db) or die(mysql_error());

while ($row=mysql_fetch_assoc($result))
{
	$selected = "";

	if ($row["test_id"] == $_POST["test_id"])
		$selected = ' selected="selected"';
		
	echo '			<option value="'. $row["test_id"] . '"'. $selected .'>'. $row["title"] .'</option>';
}
?>
		</select>
	</div>

	<div class="row">
<?php
if (!$is_passscore_in_basetable)
{
?>
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('pass_score'); ?><br />
		<input type="radio" name="pass_score" value="1" id="percentage"  <?php if ($_POST['passpercent'] <> 0){echo 'checked="true"';} ?>
		 onfocus="disable_texts('points');" />

		<input type="text" name="passpercent" id="passpercent" size="2" value="<?php echo $_POST['passpercent']; ?>" 
		 <?php if ($_POST['passpercent'] == 0){echo 'disabled="true"';} ?> /> 
		<label for="percentage" title="<?php echo _AT('pass_score'). ': '. _AT('percentage_score');  ?>"><?php  echo '% ' . _AT('percentage_score'); ?></label><br />

		<input type="radio" name="pass_score" value="2" id="points"  <?php if ($_POST['passscore'] <> 0){echo 'checked="true"';} ?>
		 onfocus="disable_texts('percentage');" />

		<input type="text" name="passscore" id="passscore" size="2" value="<?php echo $_POST['passscore']; ?>" 
		 <?php if ($_POST['passscore'] == 0){echo 'disabled="true"';} ?>/> 
		<label for="points" title="<?php echo _AT('pass_score'). ': '. _AT('points_score');  ?>"><?php  echo _AT('points_score'); ?></label>
<?php
}
?>
	</div>

	<div class="row">
		<label for="certificate_template"><?php echo _AT('certificate_template'). ":"; ?></label>
		<?php echo $default_certificate; ?>
	</div>
	
	<div class="row">
		<label for="organization"><?php echo _AT('organization_name'); ?></label><br>
		<input type="text" name="organization" id="organization" size="50" value="<?php if (!isset($_POST["organization"])) echo $default_organization; else echo $_POST["organization"]; ?>" />
	</div>
	
	<div class="row">
		<label for="enable_download"><?php echo _AT('enable_download_certificate'); ?></label><br>

		<input type="radio" name="enable_download" value="0" id="no"  <?php if ($_POST['enable_download'] == 0){echo 'checked="true"';} ?> />
		<label for="no"><?php  echo _AT('no'); ?></label><br />

		<input type="radio" name="enable_download" value="1" id="ok"  <?php if ($_POST['enable_download'] == 1){echo 'checked="true"';} ?> />
		<label for="ok"><?php  echo _AT('issue_certificate'); ?></label><br />
	</div>

	<div class="row">
		<label for="certificate_text"><?php echo _AT('certificate_text'); ?></label><br>
		<table summary="" class="data" rules="cols" align="center" style="width: 100%;">
			<thead>
			<tr>
				<th scope="col"><?php echo _AT('field_name'); ?></th>
				<th scope="col"><?php echo _AT('field_value'); ?></th>
			</tr>
			</thead>

			<tbody>
<?php
for ($i=0; $i < count($fields_array); $i++)
{
?>
			<tr>
				<td style="width:20%"><?php echo $fields_array[$i]["FieldName"]; ?></td>
				<td style="width:80%"><textarea name="fields_value[]" rows="1" cols="120" style="max-width:100%"><?php if (!isset($_POST["fields_value"][$i])) echo $fields_array[$i]["FieldValue"]; else echo $_POST["fields_value"][$i]; ?></textarea></td>
			</tr>
<?php
}
?>
			</tbody>
		</table>
		<small>&middot; <?php echo _AT('certificate_tokens'); ?></small>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
	</div>

</div>
</form>

<script language="javascript" type="text/javascript">
function disable_texts (name) {
	if (name == 'both')
	{
		document.form['passpercent'].disabled=true;
		document.form['passscore'].disabled=true;
		document.form['passpercent'].value=0;
		document.form['passscore'].value=0;
	}
	else if (name == 'percentage')
	{
		document.form['passpercent'].disabled=true;
		document.form['passpercent'].value=0;
		document.form['passscore'].disabled=false;
	}
	else if (name == 'points')
	{
		document.form['passpercent'].disabled=false;
		document.form['passscore'].disabled=true;
		document.form['passscore'].value=0;
	}
}
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
