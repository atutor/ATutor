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

/************************************************************************/
/*
/* This script is called by gradebook/grade_scale_add.php, gradebook/grade_scale_edit.php
/* to add/edit grade scales
/*
/* Required parameter: $action: "add" or "edit"
/*
/************************************************************************/

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

require('lib/gradebook.inc.php');

if (isset($_POST['action'])) $action = $_POST['action'];

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: grade_scale.php');
	exit;
} 
else if (isset($_POST['submit'])) 
{
	$_POST['scale_name']    = trim($_POST['scale_name']);

	$empty_fields = array();
	if ($_POST['scale_value'][0] == '') 
	{
		$empty_fields[] = _AT('scale_value').' at line 1';
	}

	if ($_POST['percentage_from'][0] == '') 
	{
		$empty_fields[] = _AT('percentage_from').' at line 1';
	}

	if ($_POST['percentage_to'][0] == '') 
	{
		$empty_fields[] = _AT('percentage_to').' at line 1';
	}

	if (!empty($empty_fields)) 
	{
		$msg->addError(array('EMPTY_FIELDS', implode(', ', $empty_fields)));
	}

	if (!$msg->containsErrors()) 
	{
		$_POST['scale_name']   = $addslashes($_POST['scale_name']);

		if ($action == "add")
		{
			$sql	= "INSERT INTO ".TABLE_PREFIX."grade_scales
			         (member_id, scale_name, created_date) 
			         VALUES (" . $_SESSION["member_id"] . ", '". $_POST["scale_name"] ."', now())";
			$result	= mysql_query($sql, $db) or die(mysql_error());
			
			$grade_scale_id = mysql_insert_id();
		}
		else if ($action == "edit" && isset($_POST["grade_scale_id"]))
		{
			$grade_scale_id = $_POST["grade_scale_id"];
			
			$sql	= "UPDATE ".TABLE_PREFIX."grade_scales
			            SET scale_name = '".$_POST["scale_name"]."'
			         WHERE grade_scale_id = ". $grade_scale_id;
			$result	= mysql_query($sql, $db) or die(mysql_error());
			
			// clean up scale details for new insertions
			$sql = "DELETE FROM ".TABLE_PREFIX."grade_scales_detail WHERE grade_scale_id = ". $grade_scale_id;
			$result	= mysql_query($sql, $db) or die(mysql_error());
		}
		
		for ($i=0; $i<10; $i++) 
		{
			if ($_POST['scale_value'][$i] <> "")
			{
				$_POST['scale_value'][$i] = $addslashes(trim($_POST['scale_value'][$i]));
				$_POST['percentage_from'][$i] = intval($_POST['percentage_from'][$i]);
				$_POST['percentage_to'][$i] = intval($_POST['percentage_to'][$i]);
	
				$sql	= "INSERT INTO ".TABLE_PREFIX."grade_scales_detail
				         (grade_scale_id, scale_value, percentage_from, percentage_to) 
				         VALUES (" . $grade_scale_id . ", '". $_POST['scale_value'][$i] ."', ".$_POST['percentage_from'][$i].", ".$_POST['percentage_to'][$i].")";

//				print $sql;
				$result	= mysql_query($sql, $db) or die(mysql_error());
			}
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: grade_scale.php');
		exit;
	}
} 
else if (isset($_POST['preset']) || ($action == 'edit' && isset($_REQUEST['grade_scale_id']))) 
{
	if (isset($_POST['selected_grade_scale_id']))
	{
		// clean up values preset previously
		unset($_POST["scale_value"]);
		unset($_POST["percentage_from"]);
		unset($_POST["percentage_to"]);
	}

	if (!$msg->containsErrors() && $_POST['selected_grade_scale_id'] > 0) 
	{
		// load preset
		$_POST['selected_grade_scale_id'] = intval($_POST['selected_grade_scale_id']);
		$sql	= "SELECT * FROM ".TABLE_PREFIX."grade_scales_detail d, ".TABLE_PREFIX."grade_scales g WHERE d.grade_scale_id = g.grade_scale_id AND d.grade_scale_id=".$_POST[selected_grade_scale_id]." ORDER BY percentage_to DESC";
	}
	else if ($action == 'edit' && isset($_REQUEST['grade_scale_id']))
	{
		// edit existing
		$sql	= "SELECT * FROM ".TABLE_PREFIX."grade_scales_detail d, ".TABLE_PREFIX."grade_scales g WHERE d.grade_scale_id = g.grade_scale_id AND d.grade_scale_id=".$_REQUEST['grade_scale_id']." ORDER BY percentage_to DESC";
	}
	
	$result	= mysql_query($sql, $db) or die(mysql_error());
	
	$i = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		$_POST["scale_name"] = $row["scale_name"];
		$_POST["scale_value"][$i] = $row["scale_value"];
		$_POST["percentage_from"][$i] = $row["percentage_from"];
		$_POST["percentage_to"][$i] = $row["percentage_to"];
		
		$i++;
	}
}

$onload = 'document.form.selected_grade_scale_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF'] . (isset($_REQUEST['grade_scale_id'])? "?grade_scale_id=".$_REQUEST['grade_scale_id'] : ""); ?>" method="post" name="form">
<input type="hidden" name="grade_scale_id" value="<?php echo $_REQUEST['grade_scale_id']; ?>" />
<input type="hidden" name="action" value="<?php echo $action; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('preset_scales'); ?></legend>

	<div class="row">
	<?php print_grade_scale_selectbox($_POST["selected_grade_scale_id"]); ?>
	</div>
	
	<div class="row buttons">
		<input type="submit" name="preset" value="<?php echo _AT('set_preset'); ?>" class="button" />
	</div>
	</fieldset>

	<fieldset  class="group_form"><legend class="group_form"><?php echo _AT('grade_scale'); ?></legend>
	
	<div>
		<label for="scale_name"><?php echo _AT('name'); ?></label><br />
		<input type="text" id="scale_name" size="30" name="scale_name" value="<?php echo htmlspecialchars(stripslashes($_POST['scale_name'])); ?>" /><br /><br />
	</div>
		<table>
<?php for ($i=0; $i<10; $i++) { ?>
		<tr>
			<td>
			</td>
			<td>
			<?php if ($i==0) { ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php } ?>
			<?php echo _AT('scale_value'); ?>
			</td>

			<td>
			<?php if ($i==0) { ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php } ?>
			<?php echo _AT('percentage_from'); ?>
			</td>

			<td>
			<?php if ($i==0) { ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php } ?>
			<?php echo _AT('percentage_to'); ?>
			</td>
		</tr>

		<tr>
			<td><?php echo $i+1; ?></td>
			<td><input type="text" id="scale_value_<?php echo $i; ?>" size="40" name="scale_value[<?php echo $i; ?>]" value="<?php echo htmlspecialchars(stripslashes($_POST['scale_value'][$i])); ?>" /></td>
			<td><input type="text" id="percentage_from_<?php echo $i; ?>" size="10" name="percentage_from[<?php echo $i; ?>]" value="<?php echo htmlspecialchars(stripslashes($_POST['percentage_from'][$i])); ?>" />%</td>
			<td><input type="text" id="percentage_to_<?php echo $i; ?>" size="10" name="percentage_to[<?php echo $i; ?>]" value="<?php echo htmlspecialchars(stripslashes($_POST['percentage_to'][$i])); ?>" />%</td>
		</tr>
<?php } ?>
		</table>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>