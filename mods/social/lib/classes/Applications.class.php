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
	 * @param	boolean		true if we only want to list the applications that we allow via the settings, false otherwise.
	 * @return hash of applications, id=>app obj
	 */
	function listMyApplications($use_settings=false){
		global $db;
		$hash = array();

		$sql = 'SELECT id, title FROM '.TABLE_PREFIX.'social_applications a, (SELECT application_id FROM '.TABLE_PREFIX.'social_members_applications WHERE member_id='.$_SESSION['member_id'].') AS apps WHERE a.id=apps.application_id';
		$result = mysql_query($sql, $db);
		$home_settings = $this->getHomeDisplaySettings();
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$app = new Application($row['id']);
				if($use_settings){
					if(!isset($home_settings[$row['id']])){
						continue;
					}
				}
				$hash[$row['id']] = $app;
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
	 * Delete applications
	 * @param	array	array of application_id to be deleted.
	 */
	function deleteApplications($ids){
		global $db;
		$id_list = implode(', ', $ids);
		$sql = 'DELETE FROM '.TABLE_PREFIX."social_applications WHERE id IN ($id_list)";
		mysql_query($sql, $db);
	}

	/**
	 * To determine which application to show on the home tab
	 * Save the settings in serialized format.
	 * @param	mixed		settings array. [note: upgrade this to  an object if needed later on]
	 */
	function setHomeDisplaySettings($settings){
		global $db, $addslashes;
		$settings = $addslashes(serialize($settings));
		$sql = 'REPLACE INTO '.TABLE_PREFIX."social_user_settings SET app_settings='".$settings."', member_id=".$_SESSION['member_id'];
		$result = mysql_query($sql, $db);
	}


	/**
	 * Return the <a> link of an application by the given id.
	 * @param	string	the title/name of this application
	 * @param	int		application id
	 * @return	THe <a> tag link of the requested application.
	 */
	function getAppLink($title, $id){
		return '<a href="'.url_rewrite('mods/social/applications.php?app_id='.$id) . '"><b>' . $title . '</b></a>';
	}

	
	/** 
	 * Get the home display setting 
	 * @return	array of settings that define which gadget to be displayed on the social home page.
	 */
	function getHomeDisplaySettings(){
		global $db;
		$sql = 'SELECT app_settings FROM '.TABLE_PREFIX.'social_user_settings WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		if ($rs){
			list($settings) = mysql_fetch_array($rs);
		}
		return unserialize($settings);
	}

}
?>