<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
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
		$hash = array();

		$sql = 'SELECT id, title FROM %ssocial_applications a, (SELECT application_id FROM %ssocial_members_applications WHERE member_id=%d) AS apps WHERE a.id=apps.application_id';
		$rows_apps = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id']));
		
		$home_settings = $this->getHomeDisplaySettings();
		if(count($rows_apps) > 0){
		    foreach($rows_apps as $row){
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
		$hash = array();

		$sql = 'SELECT * FROM %ssocial_applications';
		$rows_apps = queryDB($sql, array(TABLE_PREFIX));
		
		foreach($rows_apps as $row){
			$hash[$row['id']] = new Application($row['id']);
		}
		return $hash;
	}

	
	/**
	 * Delete applications
	 * @param	array	array of application_id to be deleted.
	 */
	function deleteApplications($ids){
		//foreach of these ids, delete all their associations
		foreach ($ids as $id){
			$app = new Application($id);
			$app->deleteApplication();
		}

		//now delete it from the application table
		$id_list = implode(',', array_map('intval', $ids));

		$sql = "DELETE FROM %ssocial_applications WHERE id IN (%s)";
		queryDB($sql, array(TABLE_PREFIX, $id_list));
	}

	/**
	 * To determine which application to show on the home tab
	 * Save the settings in serialized format.
	 * @param	mixed		settings array. [note: upgrade this to  an object if needed later on]
	 */
	function setHomeDisplaySettings($settings){
		$settings = serialize($settings);
		$sql = "REPLACE INTO %ssocial_user_settings SET app_settings='%s', member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $settings, $_SESSION['member_id']));
	}


	/**
	 * Return the <a> link of an application by the given id.
	 * @param	string	the title/name of this application
	 * @param	int		application id
	 * @return	THe <a> tag link of the requested application.
	 */
	function getAppLink($title, $id){
		return '<a href="'.url_rewrite(AT_SOCIAL_BASENAME.'applications.php?app_id='.$id) . '"><b>' . $title . '</b></a>';
	}

	
	/** 
	 * Get the home display setting 
	 * @return	array of settings that define which gadget to be displayed on the social home page.
	 */
	function getHomeDisplaySettings(){
		$sql = 'SELECT app_settings FROM %ssocial_user_settings WHERE member_id=%d';
		$rs = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']),TRUE ,'', '', MYSQL_NUM);
		if(count($rs) > 0){
			list($settings) = $rs;
		}
		return unserialize($settings);
	}

}
?>