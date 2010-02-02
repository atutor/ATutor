<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: new_reading_av.php 7482 2008-05-06 17:44:49Z greg $
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

$existing = -1;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['existing']   = intval($_POST['existing']);
	$existing            = $_POST['existing'];
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

	$sql = "INSERT INTO ".TABLE_PREFIX."reading_list VALUES (NULL, $_SESSION[course_id],
		'$_POST[existing]',
		'$_POST[readstatus]',
		'$date_start',
		'$date_end',
		'$_POST[comment]'
		)";
	$result = mysql_query($sql,$db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index_instructor.php');
	exit;
}

if (isset($_GET['existing'])){
	$existing = intval ($_GET['existing']);
}

$today = getdate();

$sql = "SELECT title, resource_id FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND type=".RL_TYPE_AV." ORDER BY title";
$av_result = mysql_query($sql, $db);

if (!mysql_num_rows($av_result)) {
	header('Location: add_resource_av.php?page_return=new_reading_av.php');
	exit;
}

$onload = 'document.form.name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('rl_av_material_to_view'); ?></legend>

	<div class="row">
		<label for="title"><?php  echo _AT('select_av'); ?>:</label>
		<select name="existing" id="title">

			<?php while ($row = mysql_fetch_assoc($av_result)): ?>
				<option value="<?php echo $row['resource_id']; ?>"<?php if ($row['resource_id'] == $existing) { echo ' selected="selected"'; } ?>><?php echo htmlspecialchars($row['title']); ?></option>
			<?php endwhile; ?>
		
		</select>

		<?php  echo _AT('rl_or'); ?> <a href="reading_list/add_resource_av.php"><?php  echo _AT('rl_create_new_av'); ?></a>
	</div>

	<div class="row">
		<input type="radio" name="readstatus" value="required" id="required" <?php
		if (isset($_POST['readstatus'])){
			if ($_POST['readstatus'] == 'required'){
				echo 'checked="checked"';
			}
		} else {
			echo 'checked="checked"';
		}?>/>
		<label for="required"><?php  echo _AT('required'); ?></label>
		<input type="radio" name="readstatus" value="optional" id="optional" <?php if (isset($_POST['readstatus']) && ($_POST['readstatus'] == 'optional')) { echo ' checked="checked"'; } ?>/>
		<label for="optional"><?php  echo _AT('optional'); ?></label>
	</div>	
	
	<div class="row">
	<label for="comment"><?php  echo _AT('comment'); ?>:</label><input type="text" id="comment" size="75" name="comment" value="<?php if (isset($_POST['comment'])) echo $stripslashes($_POST['comment']);  ?>" />
	</div>

<h3><?php  echo _AT('rl_read_by_date'); ?></h3>

	<div class="row">
		<input type="radio" id="nodate" name="hasdate" value="false" <?php
		if (isset($_POST['hasdate'])){
			if ($_POST['hasdate'] != 'true'){
				echo ' checked="checked"';
			}
		} else {
			echo ' checked="checked"';
		}?>/>
		<label for="nodate"><?php  echo _AT('rl_no_read_by_date'); ?></label>
	</div>

	<div class="row">
		<input type="radio" id="hasdate" name="hasdate" value="true" <?php if (isset($_POST['hasdate']) && ($_POST['hasdate'] == 'true')) { echo ' checked="checked"'; } ?>/>
		<label for="hasdate"><?php  echo _AT('rl_reading_date'); ?></label><br/>

		<label for="startdate"><?php  echo _AT('start_date'); ?>:</label>

		<select name="startday" id="startdate">
		<?php for ($i = 1; $i <= 31; $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['mday']) { echo ' selected="selected"'; } ?>><?php echo $i ?></option>
		<?php } ?>
		</select>
		
		<select name="startmonth">
		<?php for ($i = 1; $i <= 12; $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['mon']) { echo ' selected="selected"'; } ?>><?php echo AT_Date('%M', $i, AT_DATE_INDEX_VALUE) ?></option>
		<?php } ?>
		</select>

		<select name="startyear">
		<?php for ($i = ($today['year'] - 1); $i <= ($today['year'] + 4); $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['year']) { echo ' selected="selected"'; } ?>><?php echo $i ?></option>
		<?php } ?>
		</select>
	
	
		<br/><label for="enddate"><?php  echo _AT('end_date'); ?>:</label>

		<select name="endday" id="enddate">
		<?php for ($i = 1; $i <= 31; $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['mday']) { echo ' selected="selected"'; } ?>><?php echo $i ?></option>
		<?php } ?>
		</select>
	
		<select name="endmonth">
		<?php for ($i = 1; $i <= 12; $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['mon']) { echo ' selected="selected"'; } ?>><?php echo AT_Date('%M', $i, AT_DATE_INDEX_VALUE) ?></option>
		<?php } ?>
		</select>
	
		<select name="endyear">
		<?php for ($i = ($today['year'] - 1); $i <= ($today['year'] + 4); $i++){ ?>
			<option value="<?php echo $i ?>" <?php if ($i == $today['year']) { echo ' selected="selected"'; } ?>><?php echo $i ?></option>
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