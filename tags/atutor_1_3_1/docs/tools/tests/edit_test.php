<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('edit_test');

	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	if ($_POST['submit']) {
		$_POST['title'] = trim($_POST['title']);
		$_POST['format']= intval($_POST['format']);
		$_POST['randomize_order']	= intval($_POST['randomize_order']);
		$_POST['num_questions']		= intval($_POST['num_questions']);

		$_POST['instructions'] = trim($_POST['instructions']);

		if ($_POST['title'] == '') {
			$errors[] = AT_ERROR_NO_TITLE;
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
			$errors[]= AT_ERROR_START_DATE_INVALID;
		}

		if (!checkdate($month_end, $day_end, $year_end)) {
			$errors[]=AT_ERROR_END_DATE_INVALID;
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

		if (!$errors) {
			$sql = "UPDATE ".TABLE_PREFIX."tests SET title='$_POST[title]', format=$_POST[format], start_date='$start_date', end_date='$end_date', randomize_order=$_POST[randomize_order], num_questions=$_POST[num_questions], instructions='$_POST[instructions]' WHERE test_id=$tid AND course_id=$_SESSION[course_id]";

			$result = mysql_query($sql, $db);

			Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_TEST_UPDATED));
			exit;
		}
	}

	require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

	echo '<h3>'._AT('edit_test').'</h3>';

	if (!$_POST['submit']) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (!($row = mysql_fetch_array($result))){
			$errors[]=AT_ERROR_TEST_NOT_FOUND;
			print_errors($errors);
			require (AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		$_POST	= $row;
	} else {
		$_POST['start_date'] = $start_date;
		$_POST['end_date']	 = $end_date;
	}


print_errors($errors);

?>
<form action="tools/tests/edit_test.php" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="hidden" name="format" value="0" />
<input type="hidden" name="randomize_order" value="1" />
<input type="hidden" name="instructions" value="" />
<input type="hidden" name="num_questions" value="1" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php echo _AT('edit_test'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><label for="title"><b><?php echo _AT('test_title'); ?>:</b></label></td>
	<td class="row1"><input type="text" name="title" id="title" class="formfield" size="40"	value="<?php 
		echo stripslashes(htmlspecialchars($_POST['title'])); ?>" /></td>
</tr>

<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="format"><b><?php echo _AT('test_format'); ?>:</b></label></td>
	<td class="row1"><select name="format" id="format">
			<option value="0" <?php if ($_POST['format'] == 0) { echo 'selected="selected"'; } ?>><?php echo _AT('one_page_test'); ?></option>
			<option value="1" <?php if ($_POST['format'] == 1) { echo 'selected="selected"'; } ?>><?php echo _AT('multi_page_test'); ?></option>
		</select></td>
</tr-->
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('start_date'); ?>:</b></td>
	<td class="row1"><?php
				
			$today_day   = substr($_POST['start_date'], 8, 2);
			$today_mon   = substr($_POST['start_date'], 5, 2);
			$today_year  = substr($_POST['start_date'], 0, 4);

			$today_hour  = substr($_POST['start_date'], 11, 2);
			$today_min   = substr($_POST['start_date'], 14, 2);

			$name = '_start';
			require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('end_date'); ?>:</b></td>
	<td class="row1"><?php
				
			$today_day   = substr($_POST['end_date'], 8, 2);
			$today_mon   = substr($_POST['end_date'], 5, 2);
			$today_year  = substr($_POST['end_date'], 0, 4);

			$today_hour  = substr($_POST['end_date'], 11, 2);
			$today_min   = substr($_POST['end_date'], 14, 2);

			$name = '_end';
			require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

	?></td>
</tr>

<!-- More question options for a future release of ATutor  -->
<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="order"><b>Randomize Order:</b></label></td>
	<td class="row1"><input type="radio" name="randomize_order" value="1" id="yes" <?php if ($_POST['randomize_order'] == 1) { echo 'checked="checked"'; } ?> /><label for="yes">yes</label>, <input type="radio" name="randomize_order" value="0" id="no" <?php if ($_POST['randomize_order'] == 0) { echo 'checked="checked"'; } ?> /><label for="no">no</label></td>
</tr-->


<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="num"><b>Number of Questions:</b></label></td>
	<td class="row1"><input type="text" id="num" name="num_questions" size="2" class="formfield" value="<?php echo $_POST['num_questions']; ?>" /> <small class="spacer">If more are available then they will be chosen at random</small></td>
</tr-->

<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><label for="inst"><b>Special Instructions:</b></label></td>
	<td class="row1"><textarea name="instructions" id="inst" class="formfield" cols="50" rows="6"><?php echo $_POST['instructions']; ?></textarea>
	<br />
	<small class="spacer">You can add special instructions that will appear before the test starts.</small><br /><br /></td>
</tr-->
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" value="<?php echo _AT('edit_test_properties');  ?>" class="button" name="submit" accesskey="s" /></td>
</tr>
</table>
</form>
<br />
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');

