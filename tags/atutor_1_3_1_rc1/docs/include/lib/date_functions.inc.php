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
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* Uses the same options as date(), but require a % infront of each argument and the
/* textual values are language dependant ( unlike date() ).

	the following options were added as language dependant:

	%D: A textual representation of a week, three letters Mon through Sun
	%F: A full textual representation of a month, such as January or March January through December
	%l (lowercase 'L'): A full textual representation of the day of the week Sunday through Saturday
	%M: A short textual representation of a month, three letters Jan through Dec
	?? %S: English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j
	?? %a: Lowercase Ante meridiem and Post meridiem am or pm 
	?? %A: Uppercase Ante meridiem and Post meridiem AM or PM 

	valid format_types:
	AT_DATE_MYSQL_DATETIME:		YYYY-MM-DD HH:MM:SS
	AT_DATE_MYSQL_TIMESTAMP_14:	YYYYMMDDHHMMSS
	AT_DATE_UNIX_TIMESTAMP:		seconds since epoch
	AT_DATE_INDEX_VALUE:			0-x, index into a date array
*/
function AT_date($format='%Y-%M-%d', $timestamp = '', $format_type=AT_DATE_MYSQL_DATETIME)
{	
	static $day_name_ext, $day_name_con, $month_name_ext, $month_name_con;

	if (!isset($day_name_ext)) {
		$day_name_ext = array(	_AT('date_sunday'), 
								_AT('date_monday'), 
								_AT('date_tuesday'), 
								_AT('date_wednesday'), 
								_AT('date_thursday'), 
								_AT('date_friday'),
								_AT('date_saturday'));

		$day_name_con = array(	_AT('date_sun'), 
								_AT('date_mon'), 
								_AT('date_tue'), 
								_AT('date_wed'),
								_AT('date_thu'), 
								_AT('date_fri'), 
								_AT('date_sat'));

		$month_name_ext = array(_AT('date_january'), 
								_AT('date_february'), 
								_AT('date_march'), 
								_AT('date_april'), 
								_AT('date_may'),
								_AT('date_june'), 
								_AT('date_july'), 
								_AT('date_august'), 
								_AT('date_september'), 
								_AT('date_october'), 
								_AT('date_november'),
								_AT('date_december'));

		$month_name_con = array(_AT('date_jan'), 
								_AT('date_feb'), 
								_AT('date_mar'), 
								_AT('date_apr'), 
								_AT('date_may_short'),
								_AT('date_jun'), 
								_AT('date_jul'), 
								_AT('date_aug'), 
								_AT('date_sep'), 
								_AT('date_oct'), 
								_AT('date_nov'),
								_AT('date_dec'));
	}

	if ($format_type == AT_DATE_INDEX_VALUE) {
		if ($format == '%D') {
			return $day_name_con[$timestamp-1];
		} else if ($format == '%l') {
			return $day_name_ext[$timestamp-1];
		} else if ($format == '%F') {
			return $month_name_ext[$timestamp-1];
		} else if ($format == '%M') {
			return $month_name_con[$timestamp-1];
		}
	}

	if ($timestamp == '') {
		$timestamp = time();
		$format_type = AT_DATE_UNIX_TIMESTAMP;
	}

	/* 1. convert the date to a Unix timestamp before we do anything with it */
	if ($format_type == AT_DATE_MYSQL_DATETIME) {
		$year	= substr($timestamp,0,4);
		$month	= substr($timestamp,5,2);
		$day	= substr($timestamp,8,2);
		$hour	= substr($timestamp,11,2);
		$min	= substr($timestamp,14,2);
		$sec	= substr($timestamp,17,2);
	    $timestamp	= mktime($hour, $min, $sec, $month, $day, $year);

	} else if ($format_type == AT_DATE_MYSQL_TIMESTAMP_14) {
	    $hour		= substr($timestamp,8,2);
	    $minute		= substr($timestamp,10,2);
	    $second		= substr($timestamp,12,2);
	    $month		= substr($timestamp,4,2);
	    $day		= substr($timestamp,6,2);
	    $year		= substr($timestamp,0,4);
	    $timestamp	= mktime($hour, $minute, $second, $month, $day, $year);  
	}

	/* pull out all the %X items from $format */
	$first_token = strpos($format, '%');
	if ($first_token === false) {
		/* no tokens found */
		return $timestamp;
	} else {
		$tokened_format = substr($format, $first_token);
	}
	$tokens = explode('%', $tokened_format);
	array_shift($tokens);
	$num_tokens = count($tokens);

	$output = $format;
	for ($i=0; $i<$num_tokens; $i++) {
		$tokens[$i] = substr($tokens[$i],0,1);

		if ($tokens[$i] == 'D') {
			$output = str_replace('%D', $day_name_con[date('w', $timestamp)],$output);
		
		} else if ($tokens[$i] == 'l') {
			$output = str_replace('%l', $day_name_ext[date('w', $timestamp)],$output);
		
		} else if ($tokens[$i] == 'F') {
			$output = str_replace('%F', $month_name_ext[date('n', $timestamp)-1],$output);		
		
		} else if ($tokens[$i] == 'M') {
			$output = str_replace('%M', $month_name_con[date('n', $timestamp)-1],$output);

		} else {

			/* this token doesn't need translating */
			$value = date($tokens[$i], $timestamp);
			if ($value != $tokens[$i]) {
				$output = str_replace('%'.$tokens[$i], $value, $output);
			} /* else: this token isn't valid. so don't replace it. Eg. try %q */
		}
	}

	return $output;
}
	
?>
