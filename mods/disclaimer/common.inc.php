<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/

// check whether user needs to or have already agreed with legal disclaimer
function agreed_legal_disclaimer() {
	global $_config, $db;
	
	// users are not required to agree with legal disclaimer
	if (!isset($_config['enable_terms_and_conditions']) || $_config['enable_terms_and_conditions'] <> 1) {
		return true;
	} else {
		// check whether the user has agreed with legal disclaimer
		$sql = "SELECT * FROM ".TABLE_PREFIX."DS_agreed_logins
		         WHERE login = '".$_SESSION['login']."'	";
		$result = mysql_query($sql, $db);
		if (mysql_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
	}
}

function save_agreed_login($login) {
	global $db;

	if ($login == '') return;
	
	$sql = "INSERT INTO ".TABLE_PREFIX."DS_agreed_logins (login)
	        VALUES ('".$login."')";
	$result = mysql_query($sql, $db);
}
?>