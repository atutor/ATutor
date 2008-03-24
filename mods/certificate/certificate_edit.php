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
// $Id: certificate_edit.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFICATE);

// Main process
// Initialize default vars
require_once("common.inc.php");
initialize_default_vars();

$is_passscore_in_basetable = is_pass_score_defined_in_base_table();

if (isset($_POST["certificate_id"])) $certificate_id = $_POST["certificate_id"];
else $certificate_id = $_REQUEST["certificate_id"];

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
   	
   	$sql = "UPDATE ".TABLE_PREFIX."certificate
   	          SET test_id=". $_POST["test_id"] .",
   	              passscore=". $passscore .",
   	              passpercent=". $passpercent .",
   	              organization='". $_POST["organization"]."',
   	              enable_download=". $_POST["enable_download"] ." 
		        WHERE certificate_id = ". $certificate_id;
		                
		$result = mysql_query($sql, $db) or die(mysql_error());
		write_to_log(AT_ADMIN_LOG_UPDATE, 'certificate', mysql_affected_rows($db), $sql);

		for ($i = 0; $i < count($_POST["text_id_init"]); $i++)
		{
	   	$sql = "UPDATE ".TABLE_PREFIX."certificate_text
	   	           SET field_value='". $_POST["fields_value"][$i] ."'
			        WHERE certificate_text_id = " . $_POST["text_id_init"][$i];

			$result = mysql_query($sql, $db) or die(mysql_error());
			write_to_log(AT_ADMIN_LOG_UPDATE, 'certificate_text', mysql_affected_rows($db), $sql);
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

// existing auto enrollment
if ($certificate_id > 0)
{
	$sql = "SELECT * FROM ".TABLE_PREFIX."certificate
	         WHERE certificate_id = " . $certificate_id;

	$result = mysql_query($sql, $db) or die(mysql_error());
	$row_certificate = mysql_fetch_assoc($result);
}
?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?certificate_id=<?php echo $certificate_id; ?>" method="post" name="form">

<input type="hidden" name="certificate_id" value="<?php echo $certificate_id; ?>">

<div class="input-form">
	<div class="row">
	<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="test_id"><?php echo _AT('choose_test'); ?></label><br>
		<select name="test_id" id="test_id">
<?php
// display test title linked with current $certificate_id
$sql	= "SELECT c.test_id, t.title FROM ".TABLE_PREFIX."certificate c, ".TABLE_PREFIX."tests t WHERE c.certificate_id=".$certificate_id." AND c.test_id = t.test_id";

$result = mysql_query($sql, $db) or die(mysql_error());
$row=mysql_fetch_assoc($result);
?>
			<option value='<?php echo $row["test_id"];?>'><?php echo $row["title"]; ?></option>
<?php
// display other test titles that don't have certificate yet
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] AND test_id not in (SELECT distinct test_id FROM ".TABLE_PREFIX."certificate) ORDER BY start_date DESC";
$result = mysql_query($sql, $db) or die(mysql_error());

while ($row=mysql_fetch_assoc($result))
{
	echo '			<option value="'. $row["test_id"] . '>'. $row["title"] .'</option>';
}
?>
		</select>
	</div>

	<div class="row">
<?php
if (!$is_passscore_in_basetable)
{
	// set radio button / text properties and value for pass score
	if (isset($_POST['passscore']))
	{
		if ($_POST['passscore'] <> 0)
		{
			$value_passscore = $_POST['passscore'];
			$checked_passscore = 'checked="true"';
		}
		else
			$disabled_passscore = 'disabled="true"';
	}
	else
	{
		if ($row_certificate["passscore"]<>0) 
		{
			$value_passscore = $row_certificate["passscore"];
			$checked_passscore = 'checked="true"';
		}
		else
			$disabled_passscore = 'disabled="true"';
	}

	// set radio button / text properties and value for pass percentage
	if (isset($_POST['passpercent']))
	{
		if ($_POST['passpercent'] <> 0)
		{
			$value_passpercent = $_POST['passpercent'];
			$checked_passpercent = 'checked="true"';
		}
		else
			$disabled_passpercent = 'disabled="true"';
	}
	else
	{
		if ($row_certificate["passpercent"]<>0) 
		{
			$value_passpercent = $row_certificate["passpercent"];
			$checked_passpercent = 'checked="true"';
		}
		else
			$disabled_passpercent = 'disabled="true"';
	}

?>
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('pass_score'); ?><br />
		<input type="radio" name="pass_score" value="1" id="percentage" <?php echo $checked_passpercent; ?>
		 onfocus="disable_texts('points');" />

		<input type="text" name="passpercent" id="passpercent" size="2" value="<?php echo $value_passpercent;?>" <?php echo $disabled_passpercent;?> /> 
		<label for="percentage" title="<?php echo _AT('pass_score'). ': '. _AT('percentage_score');  ?>"><?php  echo '% ' . _AT('percentage_score'); ?></label><br />

		<input type="radio" name="pass_score" value="2" id="points"  <?php echo $checked_passscore; ?>
		 onfocus="disable_texts('percentage');" />

		<input type="text" name="passscore" id="passscore" size="2" value="<?php echo $value_passscore;?>" <?php echo $disabled_passscore;?> /> 
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
		<input type="text" name="organization" id="organization" size="50" value='<?php if (!isset($_POST["organization"])) echo $row_certificate["organization"]; else echo $_POST["organization"]; ?>' />
	</div>
	
	<div class="row">
		<label for="enable_download"><?php echo _AT('enable_download_certificate'); ?></label><br>

		<input type="radio" name="enable_download" value="0" id="no"  
<?php 
if (!isset($_POST['enable_download']))
{
	if ($row_certificate["enable_download"] == 0) echo 'checked="true"';
}
else if ($_POST['enable_download'] == 0) echo 'checked="true"'; 
?> />
		<label for="no"><?php  echo _AT('no'); ?></label><br />

		<input type="radio" name="enable_download" value="1" id="ok"  
<?php 
if (!isset($_POST['enable_download']))
{
	if ($row_certificate["enable_download"] == 1) echo 'checked="true"';
}
else if ($_POST['enable_download'] == 1) echo 'checked="true"'; 
?> />

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
$sql	= "SELECT * FROM ".TABLE_PREFIX."certificate_text c WHERE c.certificate_id=".$certificate_id;
$result = mysql_query($sql, $db) or die(mysql_error());
while ($row_text=mysql_fetch_assoc($result))
{
	$text_id_init[]=$row_text["certificate_text_id"];
	$fields_name_init[]=$row_text["field_name"];
	$fields_value_init[]=$row_text["field_value"];
}

for($i=0; $i<mysql_num_rows($result); $i++)
{
?>
			<tr>
				<input type="hidden" name="text_id_init[]" value="<?php echo $text_id_init[$i]; ?>">
				<input type="hidden" name="fileds_name[]" value="<?php echo $fields_name_init[$i]; ?>">
				<td style="width:20%"><?php echo $fields_name_init[$i]; ?></td>
				<td style="width:80%"><textarea name="fields_value[]" rows="1" cols="120" style="max-width:100%"><?php if (isset($_POST["fields_value"][$i])) echo $_POST["fields_value"][$i]; else echo $fields_value_init[$i]; ?></textarea></td>
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
