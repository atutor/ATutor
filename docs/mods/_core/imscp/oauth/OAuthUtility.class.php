<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: OAuthUtility.class.php 8901 2009-11-11 19:10:19Z cindy $

/**
* OAuth Utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('AT_INCLUDE_PATH')) exit;

class OAuthUtility {

	/**
	* This function checks whether the given URL is accessible.
	* @access  public
	* @param   URL
	* @return  true if accessible, otherwise, false
	* @author  Cindy Qi Li
	*/
	public static function isAccessible($URL)
	{
		if (!@file_get_contents($URL))
			return false;
		return true;
	}

	/**
	 * This function checks whether the last access token for the current user
	 * is expired. If not, return it, otherwise, return empty.
	 * @access public
	 * @param  none 
	 * @return the access token if it's not expired, otherwise, empty.
	 * @author Cindy Qi Li
	 */
	public static function getUnexpiredAccessToken()
	{
		global $db;
		
		$sql = "SELECT token, 
		               unix_timestamp(now()) now_timestamp, 
		               unix_timestamp(addtime(oct.assign_date, ocs.expire_threshold)) expire_timestamp
		          FROM ".TABLE_PREFIX."oauth_client_servers ocs, ".TABLE_PREFIX."oauth_client_tokens oct
		         WHERE ocs.oauth_server_id=oct.oauth_server_id
		           AND oct.member_id=".$_SESSION['member_id']."
		           AND oct.token_type='access'
		         ORDER BY oct.assign_date DESC";
		
		$result = mysql_query($sql, $db);
		
		if (mysql_num_rows($result) == 0) return '';
		else
		{
			$row = mysql_fetch_assoc($result);
			
			if ($row['now_timestamp'] < $row['expire_timestamp'])
				return $row['token'];
			else
				return '';
		}
	}
}
?>
