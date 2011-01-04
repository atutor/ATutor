<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: ims_import.php 10319 2010-10-08 20:43:03Z hwong $

/**
* This class contains all the utility functions to set/get/clean progress 
* information
* @access	public
* @author	Cindy Qi Li
*/
if (!defined('AT_INCLUDE_PATH')) exit;

class StudentToolsUtil {

	/**
	 * Return an array of all the student tools for current course.
	 * Return an empty array if no student tools defined.
	 */
	public static function getStudentTools($course_id) 
	{
		global $db;
		
		$sql = "SELECT links FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id=$course_id";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$student_tools = explode('|', $row['links']);
		}
		// removed the empty array value
		$cleaned_student_tools = array();
		
		foreach ($student_tools as $tool) {
			if (trim($tool) <> '') $cleaned_student_tools[] = $tool;
		}
		
		return $cleaned_student_tools;
	}
}
?>