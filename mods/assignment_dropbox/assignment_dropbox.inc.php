<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
 * The Assignment Dropbox is designed for instructors to manage assignment 
 * submissions and for students to submit assignments.
 *
 * This file contains the functions used by Assignment Dropbox.
 **/

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
 * given an owner_type and owner_id
 * returns false if user cannot read or write to this workspace
 * returns WORKSPACE_AUTH_READ if the user can read
 * returns WORKSPACE_AUTH_WRITE if the user can write
 */
function ad_authenticate($owner_id) {
	if (authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN))
	{ 
		// instructors have read only access to assignments
		return true;
	}
	else
	{ 
		// students have read access to their own assignments
		global $db;
		$sql = "SELECT COUNT(*) cnt FROM ".TABLE_PREFIX."files
		         WHERE owner_id =".$owner_id."
                   AND owner_type= ".WORKSPACE_ASSIGNMENT."
                   AND member_id = ".$_SESSION['member_id'];
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		
		if ($row['cnt'] > 0) RETURN true;
		
		// enrolled students can submit the assignments that assign to him/her
		if ($_SESSION['member_id'] && $_SESSION['enroll']) {
			// assignments that are assigned to all students
			$sql = "SELECT count(*) cnt FROM ".TABLE_PREFIX."assignments 
                     WHERE assignment_id = ".$owner_id."
                       AND assign_to=0 
                       AND course_id=".$_SESSION[course_id];
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			
			if ($row['cnt'] > 0) RETURN true;

			// assignments that are assigned to a group, 
			// and this group has "file storage" tool available
			// and the student is in this group
			$groups_list = implode(',',$_SESSION['groups']);  // the groups that the student belongs to
			$sql = "SELECT count(*) cnt
		              FROM ".TABLE_PREFIX."groups_types gt, ".TABLE_PREFIX."groups g, ".TABLE_PREFIX."assignments a
		             WHERE g.group_id in (".$groups_list.")
		               AND g.group_id in (SELECT group_id FROM ".TABLE_PREFIX."file_storage_groups)
		               AND g.type_id = gt.type_id
		               AND gt.course_id = $_SESSION[course_id]
		               AND gt.type_id = a.assign_to
		               AND a.assignment_id = ".$owner_id;
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			
			if ($row['cnt'] > 0) RETURN true;
		}
	}

	return false;
}

?>