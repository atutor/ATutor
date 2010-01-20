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
// $Id: edit_test.php 8788 2009-09-09 17:52:52Z hwong $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields             = array();
	$_POST['title']				= $addslashes(trim($_POST['title']));
	$_POST['description']  = $addslashes(trim($_POST['description']));
	$_POST['format']			= intval($_POST['format']);
	$_POST['randomize_order']	= intval($_POST['randomize_order']);
	$_POST['num_questions']		= intval($_POST['num_questions']);
	$_POST['passpercent']	= intval($_POST['passpercent']);
	$_POST['passscore']	= intval($_POST['passscore']);
	$_POST['passfeedback']  = $addslashes(trim($_POST['passfeedback']));
	$_POST['failfeedback']  = $addslashes(trim($_POST['failfeedback']));
	$_POST['num_takes']			= intval($_POST['num_takes']);
	$_POST['anonymous']			= intval($_POST['anonymous']);
	$_POST['display']			= intval($_POST['display']);
	$_POST['allow_guests']      = $_POST['allow_guests'] ? 1 : 0;
	$_POST['show_guest_form']   = $_POST['show_guest_form'] ? 1 : 0;
	$_POST['instructions']      = $addslashes($_POST['instructions']);
	$_POST['result_release']	= intval($_POST['result_release']); 

	/* this doesn't actually get used: */
	$_POST['difficulty'] = intval($_POST['difficulty']);
	if ($_POST['difficulty'] == '') {
		$_POST['difficulty'] = 0;
	}

	$_POST['content_id'] = intval($_POST['content_id']);
	if ($_POST['content_id'] == '') {
		$_POST['content_id'] = 0;
	}


	$_POST['instructions'] = trim($_POST['instructions']);

	if ($_POST['title'] == '') {
		$missing_fields[] = _AT('title');
	}

	if ($_POST['random'] && !$_POST['num_questions']) {
		$missing_fields[] = _AT('num_questions_per_test');
	}

	if ($_POST['pass_score']==1 && !$_POST['passpercent']) {
		$missing_fields[] = _AT('percentage_score');
	}

	if ($_POST['pass_score']==2 && !$_POST['passscore']) {
		$missing_fields[] = _AT('points_score');
	}


	/* 
	 * If test is anonymous and have submissions, then we don't permit changes.
	 * This addresses the following issue: http://www.atutor.ca/atutor/mantis/view.php?id=3268
	 * TODO:	Add an extra column in test_results to remember the state of anonymous submissions.
	 *			make changes accordingly on line 255 as well.
	 */
	$sql = "SELECT t.test_id, anonymous FROM ".TABLE_PREFIX."tests_results r NATURAL JOIN ".TABLE_PREFIX."tests t WHERE r.test_id = t.test_id AND r.test_id=$tid";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		//If there are submission(s) for this test, anonymous field will not be altered.
		$_POST['anonymous'] = $row['anonymous'];
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$day_start	= intval($_POST['day_start']);
	$month_start= intval($_POST['month_start']);
	$year_start	= intval($_POST['year_start']);
	$hour_start	= intval($_POST['hour_start']);
	$min_start	= intval($_POST['min_start']);

	$day_end	= intval($_POST['day_end']);
	$month_end	= intval($_POST['month_end']);
	$year_end	= intval($_POST['year_end']);
	$hour_end	= intval($_POST['hour_end']);
	$min_end	= intval($_POST['min_end']);

	if (!checkdate($month_start, $day_start, $year_start)) {
		$msg->addError('START_DATE_INVALID');
	}

	if (!checkdate($month_end, $day_end, $year_end)) {
		$msg->addError('END_DATE_INVALID');
	}

	if (mktime($hour_end,   $min_end,   0, $month_end,   $day_end,   $year_end) < 
		mktime($hour_start, $min_start, 0, $month_start, $day_start, $year_start)) {
			$msg->addError('END_DATE_INVALID');
	}

	if (strlen($month_start) == 1){
		$month_start = "0$month_start";
	}
	if (strlen($day_start) == 1){
		$day_start = "0$day_start";
	}
	if (strlen($hour_start) == 1){
		$hour_start = "0$hour_start";
	}
	if (strlen($min_start) == 1){
		$min_start = "0$min_start";
	}
	if (strlen($month_end) == 1){
		$month_end = "0$month_end";
	}
	if (strlen($day_end) == 1){
		$day_end = "0$day_end";
	}
	if (strlen($hour_end) == 1){
		$hour_end = "0$hour_end";
	}
	if (strlen($min_end) == 1){
		$min_end = "0$min_end";
	}

	$start_date = "$year_start-$month_start-$day_start $hour_start:$min_start:00";
	$end_date	= "$year_end-$month_end-$day_end $hour_end:$min_end:00";

	if (!$msg->containsErrors()) {
		// just to make sure we own this test:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if ($row = mysql_fetch_assoc($result)) {
			if ($_POST['random']) {
				$total_weight = get_total_weight($tid, $_POST['num_questions']);
			} else {
				$total_weight = get_total_weight($tid);
			}
			//If title exceeded database defined length, truncate it.
			$_POST['title'] = validate_length($_POST['title'], 100);

			$sql = "UPDATE ".TABLE_PREFIX."tests " . 
			       "SET title='$_POST[title]', 
			            description='$_POST[description]', 
			            format=$_POST[format], 
			            start_date='$start_date', 
			            end_date='$end_date', 
			            randomize_order=$_POST[randomize_order], 
			            num_questions=$_POST[num_questions], 
			            instructions='$_POST[instructions]', 
			            content_id=$_POST[content_id],  
			            passscore=$_POST[passscore], 
		              passpercent=$_POST[passpercent], 
		              passfeedback='$_POST[passfeedback]', 
		              failfeedback='$_POST[failfeedback]', 
			            result_release=$_POST[result_release], 
			            random=$_POST[random], 
			            difficulty=$_POST[difficulty], 
			            num_takes=$_POST[num_takes], 
			            anonymous=$_POST[anonymous], 
			            guests=$_POST[allow_guests], 
			            show_guest_form=$_POST[show_guest_form],
			            out_of=$total_weight, 
			            display=$_POST[display] 
			        WHERE test_id=$tid 
			        AND course_id=$_SESSION[course_id]";
			        
			$result = mysql_query($sql, $db);

			$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE test_id=$tid";
			$result = mysql_query($sql, $db);	
			
			if (isset($_POST['groups'])) {
				$sql = "INSERT INTO ".TABLE_PREFIX."tests_groups VALUES ";
				foreach ($_POST['groups'] as $group) {
					$group = intval($group);
					$sql .= "($tid, $group),";
				}
				$sql = substr($sql, 0, -1);
				$result = mysql_query($sql, $db);
			}
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');		
		
		header('Location: index.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['submit'])) {
	$sql	= "SELECT *, DATE_FORMAT(start_date, '%Y-%m-%d %H:%i:00') AS start_date, DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:00') AS end_date FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_assoc($result))){
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
	$_POST['allow_guests'] = $row['guests'];
} else {
	$_POST['start_date'] = $start_date;
	$_POST['end_date']	 = $end_date;
}
	
$msg->printErrors();

?>

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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="hidden" name="randomize_order" value="1" />
<input type="hidden" name="instructions" value="" />
<input type="hidden" name="difficulty" value="0" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_test'); ?></legend>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" size="40" value="<?php echo stripslashes(htmlspecialchars($_POST['title'])); ?>" />
	</div>
	
	<div class="row">
		<label for="description"><?php echo _AT('test_description'); ?></label><br />
		<textarea name="description" cols="35" rows="3" id="description"><?php echo htmlspecialchars($_POST['description']); ?></textarea>
	</div>

	<div class="row">	
		<label for="num_t"><?php echo _AT('num_takes_test'); ?></label><br />
		<select name="num_takes" id="num_t">
			<option value="<?php echo AT_TESTS_TAKE_UNLIMITED; ?>" <?php if ($_POST['num_takes'] == AT_TESTS_TAKE_UNLIMITED) { echo 'selected="selected"'; } ?>><?php echo _AT('unlimited'); ?></option>
		
			<option value="1"<?php if ($_POST['num_takes'] == 1) { echo ' selected="selected"'; } ?>>1</option>
			<option value="2"<?php if ($_POST['num_takes'] == 2) { echo ' selected="selected"'; } ?>>2</option>
			<option value="3"<?php if ($_POST['num_takes'] == 3) { echo ' selected="selected"'; } ?>>3</option>
			<option value="4"<?php if ($_POST['num_takes'] == 4) { echo ' selected="selected"'; } ?>>4</option>
			<option value="5"<?php if ($_POST['num_takes'] == 5) { echo ' selected="selected"'; } ?>>5</option>
			<option value="6"<?php if ($_POST['num_takes'] == 6) { echo ' selected="selected"'; } ?>>6</option>
			<option value="7"<?php if ($_POST['num_takes'] == 7) { echo ' selected="selected"'; } ?>>7</option>
			<option value="8"<?php if ($_POST['num_takes'] == 8) { echo ' selected="selected"'; } ?>>8</option>
			<option value="9"<?php if ($_POST['num_takes'] == 9) { echo ' selected="selected"'; } ?>>9</option>
			<option value="10"<?php if ($_POST['num_takes'] == 10) { echo ' selected="selected"'; } ?>>10</option>
			<option value="15"<?php if ($_POST['num_takes'] == 15) { echo ' selected="selected"'; } ?>>15</option>
			<option value="20"<?php if ($_POST['num_takes'] >= 20) { echo ' selected="selected"'; } ?>>20</option>
		</select>
	</div>
	
	<div class="row">
		<?php echo _AT('available_on_my_courses'); ?><br />
		<?php 
			if ($_POST['format'] == 1) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
			}
		?>
		<input type="radio" name="format" id="formatN" value="0" <?php echo $n; ?> /><label for="formatN"><?php echo _AT('no'); ?></label> 
		<input type="radio" name="format" id="formatY" value="1" <?php echo $y; ?> /><label for="formatY"><?php echo _AT('yes'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('anonymous_test'); ?><br />
		<?php 
			if ($_POST['anonymous'] == 1) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
			}
		?>
		<?php
			// This addresses the following issue: http://www.atutor.ca/atutor/mantis/view.php?id=3268
			// Ref: line 64
			$sql = "SELECT t.test_id, anonymous FROM ".TABLE_PREFIX."tests_results r NATURAL JOIN ".TABLE_PREFIX."tests t WHERE r.test_id = t.test_id AND r.test_id=$tid";
			$result	= mysql_query($sql, $db);
			$anonymous_disabled = '';
			if ($row = mysql_fetch_assoc($result)) {
				//If there are submission(s) for this test, anonymous field will not be altered.
				$anonymous_disabled = 'disabled';
			}
		?>
		<input type="radio" name="anonymous" id="anonN" value="0" <?php echo $n; ?> <?php echo $anonymous_disabled; ?> /><label for="anonN"><?php echo _AT('no'); ?></label>
		<input type="radio" name="anonymous" value="1" id="anonY" <?php echo $y; ?> <?php echo $anonymous_disabled; ?> /><label for="anonY"><?php echo _AT('yes'); ?></label>
		<?php
			if ($anonymous_disabled != ""){
				echo '('._AT('disabled').')';
			}
		?>
	</div>


	<div class="row">
		<?php echo _AT('allow_guests'); ?><br />
		<?php 
			if ($_POST['allow_guests'] == 1) {
				$y = 'checked="checked"';
				$n = '';
				$disable_show_guest_form = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
				$disable_show_guest_form = 'disabled="disabled"';
			}
		?>

		<input type="radio" name="allow_guests" id="allow_guestsN" value="0" <?php echo $n; ?> onfocus="document.form.show_guest_form.checked=false; document.form.show_guest_form.disabled=true;" /><label for="allow_guestsN"><?php echo _AT('no'); ?></label> 
		<input type="radio" name="allow_guests" value="1" id="allow_guestsY" <?php echo $y; ?> onfocus="document.form.show_guest_form.disabled=false;" /><label for="allow_guestsY"><?php echo _AT('yes'); ?></label>
    <br />
		<?php 
			if ($_POST['show_guest_form'] == 1)
				$y = 'checked="checked"';
			else
				$y = '';
		?>

		<input type="checkbox" name="show_guest_form" id="show_guest_form" value="1" <?php echo $y . ' '. $disable_show_guest_form; ?> /><label for="show_guest_form"><?php echo _AT('show_guest_form'); ?></label> 
	</div>

	<div class="row">
		<?php echo _AT('display'); ?><br />
		<?php 
			if ($_POST['display'] == 1) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
			}
		?>

		<input type="radio" name="display" id="displayN" value="0" <?php echo $n; ?> /><label for="displayN"><?php echo _AT('all_questions_on_page'); ?></label> 
		<input type="radio" name="display" id="displayY" value="1" <?php echo $y; ?> /><label for="displayY"><?php echo _AT('one_question_per_page'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('pass_score'); ?><br />
		<input type="radio" name="pass_score" value="0" id="no" <?php if ($_POST['passpercent'] == 0 && $_POST['passscore'] == 0){echo 'checked="true"';} ?> 
		 onfocus="disable_texts('both');" />

		<label for="no" title="<?php echo _AT('pass_score'). ': '. _AT('no_pass_score');  ?>"><?php echo _AT('no_pass_score'); ?></label><br />

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
	</div>

	<div class="row">
		<label for="passfeedback"><?php echo _AT('pass_feedback'); ?></label><br />
		<textarea name="passfeedback" cols="35" rows="1" id="passfeedback"><?php echo htmlspecialchars($_POST['passfeedback']); ?></textarea>
	</div>

	<div class="row">
		<label for="failfeedback"><?php echo _AT('fail_feedback'); ?></label><br />
		<textarea name="failfeedback" cols="35" rows="1" id="failfeedback"><?php echo htmlspecialchars($_POST['failfeedback']); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('result_release'); ?><br />
		<?php 
			if ($_POST['result_release'] == AT_RELEASE_IMMEDIATE) {
				$check_marked = $check_never = '';
				$check_immediate = 'checked="checked"';

			} else if ($_POST['result_release'] == AT_RELEASE_MARKED) {
				$check_immediate = $check_never = '';
				$check_marked = 'checked="checked"';

			} else if ($_POST['result_release'] == AT_RELEASE_NEVER) {
				$check_immediate = $check_marked = '';
				$check_never = 'checked="checked"';
			}
		?>

		<input type="radio" name="result_release" id="release1" value="<?php echo AT_RELEASE_IMMEDIATE; ?>" <?php echo $check_immediate; ?> /><label for="release1"><?php echo _AT('release_immediate'); ?></label><br />
		<input type="radio" name="result_release" id="release2" value="<?php echo AT_RELEASE_MARKED; ?>" <?php echo $check_marked; ?> /><label for="release2"><?php echo _AT('release_marked'); ?></label><br />
		<input type="radio" name="result_release" id="release3" value="<?php echo AT_RELEASE_NEVER; ?>" <?php echo $check_never; ?>/><label for="release3"><?php echo _AT('release_never'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('randomize_questions'); ?><br />
		<?php 
			if ($_POST['random'] == 1) {
				$y = 'checked="checked"';
				$n = $disabled = '';
			} else {
				$y = '';
				$n = 'checked="checked"';
				$disabled = 'disabled="disabled" ';
			}
		?>
		<input type="radio" name="random" id="random" value="0" checked="checked" onfocus="document.form.num_questions.disabled=true;" /><label for="random"><?php echo _AT('no'); ?></label>. <input type="radio" name="random" value="1" id="ry" onfocus="document.form.num_questions.disabled=false;" <?php echo $y; ?> /><label for="ry"><?php echo _AT('yes'); ?></label>, <input type="text" name="num_questions" id="num_questions" size="2" value="<?php echo $_POST['num_questions']; ?>" <?php echo $disabled . $n; ?> /> <label for="num_questions"><?php echo _AT('num_questions_per_test'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('start_date'); ?><br />
		<?php
			$today_day   = substr($_POST['start_date'], 8, 2);
			$today_mon   = substr($_POST['start_date'], 5, 2);
			$today_year  = substr($_POST['start_date'], 0, 4);

			$today_hour  = substr($_POST['start_date'], 11, 2);
			$today_min   = substr($_POST['start_date'], 14, 2);

			$name = '_start';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</div>
	<div class="row">
		<?php echo _AT('end_date'); ?><br />
		<?php
			$today_day   = substr($_POST['end_date'], 8, 2);
			$today_mon   = substr($_POST['end_date'], 5, 2);
			$today_year  = substr($_POST['end_date'], 0, 4);

			$today_hour  = substr($_POST['end_date'], 11, 2);
			$today_min   = substr($_POST['end_date'], 14, 2);

			$name = '_end';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</div>

	<div class="row">
		<label for="inst"><?php echo _AT('limit_to_group'); ?></label><br />
		<?php
			//show groups
			//get groups currently allowed
			$current_groups = array();
			$sql	= "SELECT group_id FROM ".TABLE_PREFIX."tests_groups WHERE test_id=$tid";
			$result	= mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)) {
				$current_groups[] = $row['group_id'];
			}

			//show groups
			$sql	= "SELECT * FROM ".TABLE_PREFIX."groups_types WHERE course_id=$_SESSION[course_id] ORDER BY title";
			$result = mysql_query($sql, $db);
			if (mysql_num_rows($result)) {
				while ($row = mysql_fetch_assoc($result)) {
					echo '<em>'.$row['title'].'</em><br />';

					$sql	= "SELECT * FROM ".TABLE_PREFIX."groups WHERE type_id=$row[type_id] ORDER BY title";
					$g_result = mysql_query($sql, $db);
					while ($grow = mysql_fetch_assoc($g_result)) {
						echo '&nbsp;<label><input type="checkbox" value="'.$grow['group_id'].'" name="groups['.$grow['group_id'].']" '; 
						if (is_array($current_groups) && in_array($grow['group_id'], $current_groups)) {
							echo 'checked="checked"';
						}
						echo '/>'.$grow['title'].'</label><br />';
					}
				}
			} else {
				echo _AT('none_found');
			}
		?>
	</div>

	<div class="row">
		<label for="inst"><?php echo _AT('instructions'); ?></label><br />
		<textarea name="instructions" cols="35" rows="3" id="inst"><?php echo htmlspecialchars($_POST['instructions']); ?></textarea>
	</div>


	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save');  ?>"  name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>