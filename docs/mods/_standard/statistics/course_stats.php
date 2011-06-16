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
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN);

$year  = intval($_GET['year']);
$month = intval($_GET['month']);

	if ($month == 0) {
		$month = date('m');
		$year  = date('Y');
	}

	$days	= array();
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_stats WHERE course_id=$_SESSION[course_id] AND MONTH(login_date)=$month AND YEAR(login_date)=$year ORDER BY login_date ASC";
	$result = mysql_query($sql, $db);
	//$today  = 1; /* we start on the 1st of the month */
	$max_total_logins = 0;
	$min_total_logins = (int) 99999999;
	$total_logins = 0;

	$empty = true;
	while ($row = mysql_fetch_array($result)) {
		$empty = false;
		$row_day = substr($row['login_date'], 8, 2);

		if (substr($row_day, 0,1) == '0') {
			$row_day = substr($row_day, 1, 1);
		}
		
		while ($today < $row_day-1) {
			$today++;
			$days[$today] = array(0, 0);
			$min_total_logins = 0;
		}

		$today = $row_day; /* skip this day in the fill-in-the-blanks-loop */
				
		$days[$row_day] = array($row['guests'], $row['members']);

		if ($max_total_logins < $row['guests']+$row['members']) {
			$max_total_logins = $row['guests']+$row['members'];
		}

		if ($min_total_logins > $row['guests']+$row['members']) {
			$min_total_logins = $row['guests']+$row['members'];
		}

		$total_logins += $row['guests']+$row['members'];
	}

	/* add zeros to the end of the month, only if it isn't the current month */
	$now_month = date('m');
	$now_year  = date('Y');
	if ( (($month < $now_month) && ($now_year == $year)) || ($now_year < $year) ) {
		$today++;
		while (checkdate($month, $today,$year)) {
			$days[$today] = array(0, 0);
			$today++;
		}
	}
	$num_days = count($days);

	if ($total_logins > 0) {
		$avg_total_logins = $total_logins/$num_days;
	} else {
		$avg_total_logins = 0;
	}

	$block_height		= 10;
	$multiplyer_height  = 5; /* should be multiples of 5 */

	if ($month == 12) {
		$next_month = 1;
		$next_year  = $year + 1;
	} else {
		$next_month = $month + 1;
		$next_year  = $year;
	}

	if ($month == 1) {
		$last_month = 12;
		$last_year  = $year - 1;
	} else {
		$last_month = $month - 1;
		$last_year  = $year;
	}
//$short_name = $month_name_con['en'][$this->month-1]; 

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('last_month', $last_month);
$savant->assign('last_year', $last_year);
$savant->assign('month', $month);
$savant->assign('next_month', $next_month);
$savant->assign('next_year', $next_year);
$savant->assign('num_days', $num_days);
$savant->assign('empty', $empty);
$savant->assign('total_logins', $total_logins);
$savant->assign('min_total_logins', $min_total_logins);
$savant->assign('max_total_logins', $max_total_logins);
$savant->assign('avg_total_logins', $avg_total_logins);
$savant->assign('days', $days);
$savant->assign('result', $result);
$savant->assign('short_name', $short_name);
$savant->display('instructor/statistics/course_stats.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>