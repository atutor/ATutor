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
// $Id: approve_authorization.php 10055 2010-06-29 20:30:24Z cindy $

require_once('OAuth.php');
require_once('../Shindig/ATutorOAuthDataStore.php');

session_name('ATutorID');
session_start();

$oauthDataStore = new ATutorOAuthDataStore();

$oauth_token = $_REQUEST['oauth_token'];
$oauth_callback = $_REQUEST['oauth_callback'];
try {	
	$oauthDataStore->authorize_request_token($oauth_token);

	// if callback was provided, append token and return to the callback
	if ($oauth_callback) {
	  $oauth_callback .= (strpos($oauth_callback, '?') === false ? '?' : '&');
	  $oauth_callback .= 'oauth_token=' . urlencode($oauth_token);
	  header('Location:' . $oauth_callback);
	} else {
	  echo "Your application is now authorized.";
	}
} catch (OAuthException $e) {
  echo $e->getMessage();
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
