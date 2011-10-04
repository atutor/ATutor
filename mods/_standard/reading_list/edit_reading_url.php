<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['id']         = intval($_POST['id']);
	$_POST['existing']   = intval($_POST['existing']);
	$_POST['hasdate']    = $addslashes($_POST['hasdate']);
	$_POST['readstatus'] = $addslashes($_POST['readstatus']);
	$_POST['comment']    = $addslashes($_POST['comment']);
	$_POST['startday']   = intval($_POST['startday']);
	$_POST['startmonth'] = intval($_POST['startmonth']);
	$_POST['startyear']  = intval($_POST['startyear']);
	$_POST['endday']     = intval($_POST['endday']);
	$_POST['endmonth']   = intval($_POST['endmonth']);
	$_POST['endyear']    = intval($_POST['endyear']);

	$date_start = '0000-00-00';
	$date_end = '0000-00-00';
	if ($_POST['hasdate'] == 'true'){
		$date_start = $_POST['startyear']. '-' .str_pad ($_POST['startmonth'], 2, "0", STR_PAD_LEFT). '-' .str_pad ($_POST['startday'], 2, "0", STR_PAD_LEFT);
		$date_end = $_POST['endyear']. '-' .str_pad ($_POST['endmonth'], 2, "0", STR_PAD_LEFT). '-' .str_pad ($_POST['endday'], 2, "0", STR_PAD_LEFT);
	}

	$sql = "UPDATE ".TABLE_PREFIX."reading_list SET resource_id='$_POST[existing]', required='$_POST[readstatus]', comment='$_POST[comment]', date_start='$date_start', date_end='$date_end' WHERE reading_id='$_POST[id]' AND course_id=$_SESSION[course_id]";

	$result = mysql_query($sql,$db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

$onload = 'document.form.name.focus();';

$today = getdate();

$_GET['id'] = intval($_GET['id']);
$reading_id = $_GET['id'];
$resource_id = 0;

// get the resource ID using the reading ID
$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] AND reading_id=$reading_id";
$result = mysql_query($sql, $db);
if ($rowreading = mysql_fetch_assoc($result)) {
	$resource_id = $rowreading['resource_id'];
}

// fill the select control using all the URL resources
$sql = "SELECT title, resource_id FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND type=".RL_TYPE_URL." ORDER BY title";
$url_result = mysql_query($sql, $db);

$num_urls = mysql_num_rows($url_result);

if ($num_urls == 0) {
	header('Location: add_resource_url.php');
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $reading_id ?>" />
<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('url_to_read'); ?></legend>

	<div class="row">
		<label for="title"><?php  echo _AT('rl_select_url'); ?>:</label>
		<select name="existing" id="title">
			<?php while ($row = mysql_fetch_assoc($url_result)): ?>
				<option value="<?php echo $row['resource_id']; ?>"<?php if ($row['resource_id'] == $resource_id) { echo ' selected="selected"'; } ?>><?php echo AT_print($row['title'], 'input.text'); ?></option>
			<?php endwhile; ?>
		</select>
	</div>

	<div class="row">
		<input type="radio" name="readstatus" value="required" id="required" <?php
		if ($rowreading['required'] == 'required'){
			echo 'checked="checked"';
		}?>/>
		<label for="required"><?php  echo _AT('required'); ?></label>
		<input type="radio" name="readstatus" value="optional" id="optional" <?php
		if ($rowreading['required'] == 'optional'){
			echo 'checked="checked"';
		}?>/>
		<label for="optional"><?php  echo _AT('optional'); ?></label>
	</div>	
	
	<div class="row">
	<label for="comment"><?php  echo _AT('comment'); ?>:</label><input type="text" id="comment" size="75" name="comment" value="<?php echo AT_print($rowreading['comment'], 'reading_list.comment');  ?>" />
	</div>

<h3><?php echo _AT('rl_read_by_date'); ?></h3>

	<div class="row">
		<input type="radio" id="nodate" name="hasdate" value="false" <?php
		if ($rowreading['date_start'] == '0000-00-00'){
			echo 'checked="checked"';
		}?>/>
		<label for="nodate"><?php  echo _AT('rl_no_read_by_date'); ?></label>
	</div>

	<div class="row">
		<input type="radio" id="hasdate" name="hasdate" value="true" <?php
		if ($rowreading['date_start'] != '0000-00-00'){
			echo 'checked="checked"';
		}?>/>
		<label for="hasdate"><?php  echo _AT('rl_reading_date'); ?></label><br/>

		<label for="startdate"><?php  echo _AT('start_date'); ?>:</label>
		<?php  $array_date_start = explode ('-', $rowreading['date_start'], 3); ?>

		<select name="startday" id="startdate">
		<?php for ($i = 1; $i <= 31; $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_start[2]) { echo ' selected="selected"'; } ?>><?php echo intval($i); ?></option>
		<?php } ?>
		</select>
		
		<select name="startmonth">
		<?php for ($i = 1; $i <= 12; $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_start[1]) { echo ' selected="selected"'; } ?>><?php echo AT_Date('%M', intval($i), AT_DATE_INDEX_VALUE) ?></option>
		<?php } ?>
		</select>

		<select name="startyear">
		<?php for ($i = ($today['year'] - '1'); $i <= ($today['year'] + '4'); $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_start[0]) { echo ' selected="selected"'; } ?>><?php echo intval($i); ?></option>
		<?php } ?>
		</select>
	
		<br/><label for="enddate"><?php  echo _AT('end_date'); ?>:</label>
		<?php  $array_date_end = explode ('-', $rowreading['date_end'], 3); ?>

		<select name="endday" id="enddate">
		<?php for ($i = 1; $i <= 31; $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_end[2]) { echo ' selected="selected"'; } ?>><?php echo intval($i); ?></option>
		<?php } ?>
		</select>
	
		<select name="endmonth">
		<?php for ($i = 1; $i <= 12; $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_end[1]) { echo ' selected="selected"'; } ?>><?php echo AT_Date('%M', intval($i), AT_DATE_INDEX_VALUE) ?></option>
		<?php } ?>
		</select>
	
		<select name="endyear">
		<?php for ($i = ($today['year'] - '1'); $i <= ($today['year'] + '4'); $i++){ ?>
			<option value="<?php echo intval($i); ?>" <?php if ($i == $array_date_end[0]) { echo ' selected="selected"'; } ?>><?php echo intval($i); ?></option>
		<?php } ?>
		</select>
	
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>