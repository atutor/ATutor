<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('add_test');


	if ($_POST['submit']) {

		$_POST['title'] = trim($_POST['title']);
		$_POST['format']= intval($_POST['format']);
		$_POST['order']	= intval($_POST['order']);
		$_POST['num']	= intval($_POST['num']);

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

			$sql = "INSERT INTO ".TABLE_PREFIX."tests VALUES (0, $_SESSION[course_id], '$_POST[title]', $_POST[format], '$start_date', '$end_date', $_POST[order], $_POST[num], '$_POST[instructions]')";

			$result = mysql_query($sql, $db);

			Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_TEST_ADDED));
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

echo '<h2>'._AT('add_test').'</h2>';


print_errors($errors);

?>
<form action="tools/tests/add_test.php" method="post" name="form">
<input type="hidden" name="format" value="0" />
<input type="hidden" name="order" value="1" />
<input type="hidden" name="num" value="0" />
<input type="hidden" name="instructions" value="" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php print_popup_help(AT_HELP_ADD_TEST);  ?><?php echo _AT('new_test');  ?></th>
</tr>
<tr>
	<td class="row1" align="right"><label for="title"><b><?php echo _AT('test_title');  ?>:</b></label></td>
	<td class="row1"><input type="text" name="title" id="title" class="formfield" size="40"	value="<?php 
		echo $_POST['title']; ?>" /></td>
</tr>
<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="format"><b>Format:</b></label></td>
	<td class="row1"><select name="format" id="format">
			<option value="0">All on one page</option>
			<option value="1">1 question per page</option>
		</select></td>
</tr-->
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
					require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

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
					require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

	?></td>
</tr>


<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="order"><b>Randomize Order:</b></label></td>
	<td class="row1"><input type="radio" name="order" value="1" id="yes" /><label for="yes">yes</label>, <input type="radio" name="order" value="0" id="no" checked="checked" /><label for="no">no</label></td>
</tr-->


<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="num"><b>Number of Questions:</b></label></td>
	<td class="row1"><input type="text" id="num" name="num" size="2" class="formfield" /> <small class="spacer">If more are available then they will be chosen at random</small></td>
</tr--> 

<!--tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><label for="inst"><b>Special Instructions:</b></label></td>
	<td class="row1"><textarea name="instructions" id="inst" class="formfield" cols="50" rows="6"></textarea>
	<br />
	<small class="spacer">You can add special instructions that will appear before the test starts.</small><br /><br /></td>
</tr-->
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
