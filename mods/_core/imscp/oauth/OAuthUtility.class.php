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
		$sql = "SELECT token, 
		               unix_timestamp(now()) now_timestamp, 
		               ocs.expire_threshold,
		               unix_timestamp(addtime(oct.assign_date, sec_to_time(ocs.expire_threshold))) expire_timestamp
		          FROM %soauth_client_servers ocs, %soauth_client_tokens oct
		         WHERE ocs.oauth_server_id=oct.oauth_server_id
		           AND oct.member_id=%d
		           AND oct.token_type='access'
		         ORDER BY oct.assign_date DESC";
		
		$row = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id']));		

		if (count($row) == 0) {
			return '';
		}
		else
		{

			if ($row['expire_threshold'] == 0 || $row['now_timestamp'] < $row['expire_timestamp']) {
				return $row['token'];
			} else {
				return '';
			}
		}
	}
}
?>
