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
if (!defined('AT_INCLUDE_PATH')) { exit; }
 
$month_names = $month_name_con['en'];

		echo '<select name="day'.$name.'">';
		for ($i = 1; $i <= 31; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $today_day) {
				echo ' selected="selected"';
			}
			echo '>';
			echo $i.'</option>';
		}
		echo '</select>';

		echo '<select name="month'.$name.'">';
		for ($i = 1; $i <= 12; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $today_mon) {
				echo ' selected="selected"';
			}
			echo '>';
			echo AT_date('%F', $i, AT_DATE_INDEX_VALUE);
			echo '</option>';
		}
		echo '</select>';

		echo '<select name="year'.$name.'">';
		for ($i = min($today_year-1, date('Y')-1); $i <= $today_year+3; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $today_year) {
				echo ' selected="selected"';
			}
			echo '>';
			echo $i.'</option>';
		}
		echo '</select> ';

		echo _AT('at').'  <select name="hour'.$name.'">';
		for ($i = 0; $i <= 23; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $today_hour) {
				echo ' selected="selected"';
			}
			echo '>';
			echo $i.'</option>';
		}
		echo '</select>:';
	
		echo '<select name="min'.$name.'">';
		for ($i = 0; $i <= 59; $i+=5) {
			echo '<option value="'.$i.'"';
			if ($i == $today_min) {
				echo ' selected="selected"';
			}
			echo '>';
			echo $i.'</option>';
		}
		echo '</select><small class="spacer"> '._AT('hours_24').'</small>';
?>