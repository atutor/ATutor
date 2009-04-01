<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

require_once(AT_SOCIAL_INCLUDE.'classes/Activity.class.php');
require_once(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
require_once(AT_SOCIAL_INCLUDE.'constants.inc.php');

/**
 * Object for Applications, (aka Gadgets)
 */

class Applications {
	//constructor
	function Applications(){}

	/** 
	 * Retrieve a list of applications' titles
	 * @return hash of applications, id=>app obj
	 */
	function listMyApplications(){
		global $db;
		$hash = array();

		$sql = 'SELECT id, title FROM '.TABLE_PREFIX.'social_applications a, (SELECT application_id FROM '.TABLE_PREFIX.'social_members_applications WHERE member_id='.$_SESSION['member_id'].') AS apps WHERE a.id=apps.application_id';
		$result = mysql_query($sql, $db);

		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$hash[$row['id']] = new Application($row['id']);
			}
		}
		return $hash;
	}

	/**
	 * Retrieve a list of all installed applications
	 */
	function listApplications(){
		global $db;
		$hash = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_applications';
		$result = mysql_query($sql, $db);

		while ($row = mysql_fetch_assoc($result)){
			$hash[$row['id']] = new Application($row['id']);
		}
		return $hash;
	}

	/**
	 * Return the application link for this given id.
	 */
	function getAppLink($title, $id){
		return '<a href="'.url_rewrite('mods/social/applications.php?app_id='.$id) . '"><b>' . $title . '</b></a>';
	}

}
?>