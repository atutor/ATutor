<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('add_test');

authenticate(AT_PRIV_TEST_CREATE);

$test_type = 'normal';

if (isset($_GET['survey']) || ($_POST['test_type'] == 'survey')) {
	$test_type = 'survey';
}

if (isset($_POST['submit'])) {
	$_POST['title']      = trim($_POST['title']);
	$_POST['num']	     = intval($_POST['num']);
	$_POST['num_takes']	 = intval($_POST['num_takes']);
	$_POST['content_id'] = intval($_POST['content_id']);

	// currently these options are ignored for tests:
	$_POST['format']       = 0;  //intval($_POST['format']);
	$_POST['order']	       = 1;  //intval($_POST['order']);
	$_POST['instructions'] = ''; //trim($_POST['instructions']);
	$_POST['difficulty']   = 0;  //intval($_POST['difficulty']); 	/* avman */
	    
	if ($_POST['title'] == '') {
		$errors[] = AT_ERROR_NO_TITLE;
	}

	if (($test_type == 'normal') && ($_POST['num_takes'] <= 0) && !isset($_POST['num_takes_infinite'])) {
		$errors[] = AT_ERROR_NUM_TAKES_WRONG;
		$_POST['num_takes'];
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
		$errors[] = AT_ERROR_START_DATE_INVALID;
	}

	if (!checkdate($month_end, $day_end, $year_end)) {
		$errors[] = AT_ERROR_END_DATE_INVALID;
	}

	if (!$errors) {
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

		if (($test_type == 'normal') && isset($_POST['num_takes_infinite'])) {
			$_POST['num_takes'] = AT_TESTS_TAKE_UNLIMITED; // LEQ to 0
		} else if ($test_type == 'survey') {
			$_POST['num_takes'] = 1;
			$_POST['automark']  = AT_MARK_UNMARKED;
		}

		
		$sql = "INSERT INTO ".TABLE_PREFIX."tests VALUES (0, $_SESSION[course_id], '$_POST[title]', $_POST[format], '$start_date', '$end_date', $_POST[order], $_POST[num], '$_POST[instructions]', $_POST[content_id], $_POST[automark], $_POST[random], $_POST[difficulty], $_POST[num_takes])";

		$result = mysql_query($sql, $db);
		header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_TEST_ADDED));
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif" class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

echo '<h2>'._AT('add_test');
if ($test_type == 'survey') { 
	echo ' ('._AT('as_survey').')'; 
}
echo '</h2>';

print_errors($errors);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="test_type" value="<?php echo $test_type; ?>" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php print_popup_help(AT_HELP_ADD_TEST);  ?><?php echo _AT('new_test');  ?></th>
</tr>
<tr>
	<td class="row1" align="right"><label for="title"><b><?php echo _AT('test_title');  ?>:</b></label></td>
	<td class="row1"><input type="text" name="title" id="title" class="formfield" size="30"	value="<?php 
		echo $_POST['title']; ?>" /></td>
</tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="num_t"><b><?php echo _AT('num_takes_test'); ?>:</b></label></td>
	<td class="row1">
	<?php if ($test_type == 'survey') { 
			echo _AT('once');
		} else { ?>
			<select name="num_takes">
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
				<option value="20"<?php if ($_POST['num_takes'] == 20) { echo ' selected="selected"'; } ?>>20</option>
				<option value="25"<?php if ($_POST['num_takes'] == 25) { echo ' selected="selected"'; } ?>>25</option>
				<option value="30"<?php if ($_POST['num_takes'] == 30) { echo ' selected="selected"'; } ?>>30</option>
				<option value="35"<?php if ($_POST['num_takes'] == 35) { echo ' selected="selected"'; } ?>>35</option>
				<option value="40"<?php if ($_POST['num_takes'] == 40) { echo ' selected="selected"'; } ?>>40</option>
				<option value="45"<?php if ($_POST['num_takes'] == 45) { echo ' selected="selected"'; } ?>>45</option>
				<option value="50"<?php if ($_POST['num_takes'] == 50) { echo ' selected="selected"'; } ?>>50</option>
				<option value="60"<?php if ($_POST['num_takes'] == 60) { echo ' selected="selected"'; } ?>>60</option>
				<option value="70"<?php if ($_POST['num_takes'] == 70) { echo ' selected="selected"'; } ?>>70</option>
				<option value="80"<?php if ($_POST['num_takes'] == 80) { echo ' selected="selected"'; } ?>>80</option>
				<option value="90"<?php if ($_POST['num_takes'] == 90) { echo ' selected="selected"'; } ?>>90</option>
				<option value="100"<?php if ($_POST['num_takes'] == 100) { echo ' selected="selected"'; } ?>>100</option>
			</select>
		<?php
		}
		echo _AT('times');
	?>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="title"><b><?php echo _AT('marking'); ?>:</b></label></td>
	<td class="row1" nowrap="nowrap">
	<?php if ($test_type == 'normal') : ?>
		<input type="radio" name="automark" id="a1" value="<?php echo AT_MARK_INSTRUCTOR; ?>" /><label for="a1"><?php echo _AT('mark_instructor'); ?></label><br />
		<input type="radio" name="automark" id="a2" value="<?php echo AT_MARK_SELF; ?>" checked="checked" /><label for="a2"><?php echo _AT('self_marking'); ?></label><br />
		<input type="radio" name="automark" id="a3" value="<?php echo AT_MARK_SELF_UNCOUNTED; ?>" /><label for="a3"><?php echo _AT('self_marking_uncounted'); ?></label>
	<?php else:
		echo _AT('not_markable');		
		endif; ?>
	<br />
	</td>
</tr>
<?php if ($test_type == 'survey'): ?>
	<input type="hidden" name="random" value="0" />
	<input type="hidden" name="num" value="0" />
<?php else: ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="random"><b><?php echo _AT('randomize_questions'); ?>:</b></label></td>
		<td class="row1"><input type="radio" name="random" id="random" value="0" checked="checked" /><label for="random"><?php echo _AT('no1'); ?></label>, <input type="radio" name="random" value="1" id="ry" /><label for="ry"><?php echo _AT('yes1'); ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="num_q"><b><?php echo _AT('num_questions_per_test'); ?>:</b></label></td>
		<td class="row1"><input type="text" name="num" id="num_q" class="formfield" size="5" value="<?php echo $_POST['num']; ?>" /></td>
	</tr>
<?php endif; ?>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('start_date');  ?>:</b></td>
	<td class="row1"><?php
				
					$today_day  = date('d');
					$today_mon  = date('m');
					$today_year = date('Y');
					$today_hour = date('H');
					$today_min  = 0;

					$name = '_start';
					require(AT_INCLUDE_PATH.'html/release_date.inc.php');

	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('end_date');  ?>:</b></td>
	<td class="row1"><?php
				
					$today_day  = date('d');
					$today_mon  = date('m');
					$today_year = date('Y');
					$today_hour = date('H');
					$today_min  = 0;
					
					$name = '_end';
					require(AT_INCLUDE_PATH.'html/release_date.inc.php');

	?><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" value="<?php echo _AT('save_test_properties'); ?> Alt-s" class="button" name="submit" accesskey="s" /></td>
</tr>
</table>
</form>
<br />
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>